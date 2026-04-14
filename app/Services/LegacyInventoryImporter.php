<?php

namespace App\Services;

use App\Models\Community;
use App\Models\Deanery;
use App\Models\Item;
use App\Models\Parish;
use App\Models\ParishPriestAssignment;
use App\Models\ParishRole;
use App\Models\Priest;
use App\Models\PriestTitle;
use App\Models\Restoration;
use App\Models\User;
use Database\Seeders\InventoryRolesSeeder;
use finfo;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LegacyInventoryImporter
{
    /**
     * @var list<string>
     */
    private array $legacyTables = [
        'sec_apps',
        'sec_groups',
        'sec_groups_apps',
        'sec_settings',
        'sec_users',
        'sec_users_groups',
        'tb_arciprestazgos',
        'tb_parroquias',
        'tb_comunidades',
        'tb_articulos',
        'tb_articulos_restauracion',
        'tb_titulos_sacerdotes',
        'tb_sacerdotes',
        'tb_cargos_parroquia',
        'tb_sacerdotes_parroquias',
    ];

    /**
     * @var array<int, string>
     */
    private array $groupToRole = [
        1 => 'technical_support',
        2 => 'diocese_manager',
        3 => 'parish_manager',
        4 => 'community_manager',
    ];

    private string $legacyConnectionName = 'legacy_import_tmp';

    public function ensureLegacyArtifacts(string $sqlPath): array
    {
        $missing = [];

        if (! is_file($sqlPath)) {
            $missing[] = "Missing legacy SQL file: {$sqlPath}";
        }

        return $missing;
    }

    public function importFromSqlDump(string $sqlPath): array
    {
        return $this->withTemporaryLegacyDatabase($sqlPath, function (string $legacyConnection): array {
            $this->purgeLegacyTablesFromPrimaryDatabase();

            return $this->importCoreData($legacyConnection);
        });
    }

    public function importMediaFromSqlDump(string $sqlPath): array
    {
        return $this->withTemporaryLegacyDatabase($sqlPath, function (string $legacyConnection): array {
            $this->purgeLegacyTablesFromPrimaryDatabase();

            return $this->importMedia($legacyConnection);
        });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function reconcileFromSqlDump(string $sqlPath): Collection
    {
        return $this->withTemporaryLegacyDatabase($sqlPath, function (string $legacyConnection): Collection {
            $this->purgeLegacyTablesFromPrimaryDatabase();

            return $this->reconcileCounts($legacyConnection);
        });
    }

    public function forcePasswordResetForAll(): int
    {
        $updated = 0;

        User::query()->chunkById(100, function (Collection $users) use (&$updated): void {
            /** @var User $user */
            foreach ($users as $user) {
                $this->forcePasswordReset($user);
                $updated++;
            }
        });

        return $updated;
    }

    public function forcePasswordReset(User $user): void
    {
        $user->forceFill([
            'password' => Hash::make(Str::password(32)),
            'force_password_reset' => true,
            'legacy_password_md5' => null,
        ])->save();
    }

    public function purgeLegacyTablesFromPrimaryDatabase(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            foreach ($this->legacyTables as $table) {
                DB::statement("DROP TABLE IF EXISTS `{$table}`");
            }
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    /**
     * @template TReturn
     *
     * @param  callable(string): TReturn  $callback
     * @return TReturn
     */
    private function withTemporaryLegacyDatabase(string $sqlPath, callable $callback)
    {
        $sql = file_get_contents($sqlPath);

        if ($sql === false) {
            throw new \RuntimeException("Unable to read SQL file: {$sqlPath}");
        }

        $defaultConnection = (string) config('database.default');
        $baseConfig = config("database.connections.{$defaultConnection}");

        if (! is_array($baseConfig)) {
            throw new \RuntimeException('Default database connection configuration was not found.');
        }

        $driver = (string) ($baseConfig['driver'] ?? '');

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            throw new \RuntimeException('Temporary import requires MySQL or MariaDB as the default connection.');
        }

        $tempDatabase = 'legacy_tmp_'.strtolower(Str::random(10));
        $tempDatabaseQuoted = $this->quoteIdentifier($tempDatabase);

        DB::connection($defaultConnection)->statement("CREATE DATABASE {$tempDatabaseQuoted} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        config([
            "database.connections.{$this->legacyConnectionName}" => array_merge($baseConfig, [
                'database' => $tempDatabase,
            ]),
        ]);

        DB::purge($this->legacyConnectionName);

        try {
            DB::connection($this->legacyConnectionName)->unprepared($sql);

            return $callback($this->legacyConnectionName);
        } finally {
            DB::disconnect($this->legacyConnectionName);
            DB::purge($this->legacyConnectionName);
            config(["database.connections.{$this->legacyConnectionName}" => null]);

            DB::connection($defaultConnection)->statement("DROP DATABASE IF EXISTS {$tempDatabaseQuoted}");
        }
    }

    private function importCoreData(string $legacyConnection): array
    {
        $this->ensureRoleCatalog();

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            $this->truncateTargetTables();

            return [
                'priest_titles' => $this->importPriestTitles($legacyConnection),
                'parish_roles' => $this->importParishRoles($legacyConnection),
                'priests' => $this->importPriests($legacyConnection),
                'deaneries' => $this->importDeaneries($legacyConnection),
                'parishes' => $this->importParishes($legacyConnection),
                'communities' => $this->importCommunities($legacyConnection),
                'items' => $this->importItems($legacyConnection),
                'restorations' => $this->importRestorations($legacyConnection),
                'parish_priest_assignments' => $this->importParishPriestAssignments($legacyConnection),
                'users' => $this->importUsers($legacyConnection),
            ];
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    private function importMedia(string $legacyConnection): array
    {
        $disk = Storage::disk('public');
        $finfo = new finfo(FILEINFO_MIME_TYPE);

        $counts = [
            'deaneries' => 0,
            'parishes' => 0,
            'communities' => 0,
            'items' => 0,
            'restorations' => 0,
            'priests' => 0,
            'users' => 0,
        ];

        $this->importBlobTable(
            legacyConnection: $legacyConnection,
            sourceTable: 'tb_arciprestazgos',
            idColumn: 'IdArciprestazgo',
            blobColumn: 'ImagenArciprestazgo',
            targetTable: 'deaneries',
            targetImageColumn: 'image_path',
            folder: 'deaneries',
            disk: $disk,
            finfo: $finfo,
            countRef: $counts['deaneries']
        );

        $this->importBlobTable(
            legacyConnection: $legacyConnection,
            sourceTable: 'tb_parroquias',
            idColumn: 'IdParroquia',
            blobColumn: 'ImagenParroquia',
            targetTable: 'parishes',
            targetImageColumn: 'image_path',
            folder: 'parishes',
            disk: $disk,
            finfo: $finfo,
            countRef: $counts['parishes']
        );

        $this->importBlobTable(
            legacyConnection: $legacyConnection,
            sourceTable: 'tb_comunidades',
            idColumn: 'IdComunidad',
            blobColumn: 'ImagenComunidad',
            targetTable: 'communities',
            targetImageColumn: 'image_path',
            folder: 'communities',
            disk: $disk,
            finfo: $finfo,
            countRef: $counts['communities']
        );

        $this->importBlobTable(
            legacyConnection: $legacyConnection,
            sourceTable: 'tb_articulos',
            idColumn: 'IdArticulo',
            blobColumn: 'ImagenArticulo',
            targetTable: 'items',
            targetImageColumn: 'image_path',
            folder: 'items',
            disk: $disk,
            finfo: $finfo,
            countRef: $counts['items']
        );

        $this->importBlobTable(
            legacyConnection: $legacyConnection,
            sourceTable: 'tb_articulos_restauracion',
            idColumn: 'IdRestauracion',
            blobColumn: 'ImagenRestauracion',
            targetTable: 'restorations',
            targetImageColumn: 'image_path',
            folder: 'restorations',
            disk: $disk,
            finfo: $finfo,
            countRef: $counts['restorations']
        );

        $this->importBlobTable(
            legacyConnection: $legacyConnection,
            sourceTable: 'tb_sacerdotes',
            idColumn: 'IdSacerdote',
            blobColumn: 'ImagenSacerdote',
            targetTable: 'priests',
            targetImageColumn: 'image_path',
            folder: 'priests',
            disk: $disk,
            finfo: $finfo,
            countRef: $counts['priests']
        );

        $this->importBlobUsers($legacyConnection, $disk, $finfo, $counts['users']);

        return $counts;
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function reconcileCounts(string $legacyConnection): Collection
    {
        $rows = [
            ['legacy' => 'tb_arciprestazgos', 'new' => 'deaneries'],
            ['legacy' => 'tb_parroquias', 'new' => 'parishes'],
            ['legacy' => 'tb_comunidades', 'new' => 'communities'],
            ['legacy' => 'tb_articulos', 'new' => 'items'],
            ['legacy' => 'tb_articulos_restauracion', 'new' => 'restorations'],
            ['legacy' => 'tb_titulos_sacerdotes', 'new' => 'priest_titles'],
            ['legacy' => 'tb_sacerdotes', 'new' => 'priests'],
            ['legacy' => 'tb_cargos_parroquia', 'new' => 'parish_roles'],
            ['legacy' => 'tb_sacerdotes_parroquias', 'new' => 'parish_priest_assignments'],
            ['legacy' => 'sec_users', 'new' => 'users'],
        ];

        return collect($rows)->map(function (array $row) use ($legacyConnection): array {
            $legacyCount = DB::connection($legacyConnection)->table($row['legacy'])->count();
            $newCount = DB::table($row['new'])->count();

            return [
                'legacy' => $row['legacy'],
                'new' => $row['new'],
                'legacy_count' => $legacyCount,
                'new_count' => $newCount,
                'match' => $legacyCount === $newCount,
            ];
        });
    }

    private function ensureRoleCatalog(): void
    {
        Artisan::call('db:seed', [
            '--class' => InventoryRolesSeeder::class,
            '--force' => true,
        ]);
    }

    private function truncateTargetTables(): void
    {
        DB::table(config('permission.table_names.model_has_roles'))->where('model_type', User::class)->delete();
        DB::table(config('permission.table_names.model_has_permissions'))->where('model_type', User::class)->delete();

        ParishPriestAssignment::query()->truncate();
        Restoration::query()->truncate();
        Item::query()->truncate();
        Community::query()->truncate();
        Parish::query()->truncate();
        Deanery::query()->truncate();
        Priest::query()->truncate();
        ParishRole::query()->truncate();
        PriestTitle::query()->truncate();
        User::query()->truncate();
    }

    private function importDeaneries(string $legacyConnection): int
    {
        $rows = DB::connection($legacyConnection)->table('tb_arciprestazgos')->get();

        foreach ($rows as $legacy) {
            Deanery::query()->updateOrCreate(
                ['id' => (int) $legacy->IdArciprestazgo],
                [
                    'name' => trim((string) $legacy->NombreArciprestazgo),
                    'email' => $this->nullableString($legacy->CorreoArciprestazgo),
                    'description' => $this->nullableString($legacy->DescripcionArciprestazgo),
                    'archpriest_id' => $legacy->Arcipestre ? (int) $legacy->Arcipestre : null,
                ]
            );
        }

        return $rows->count();
    }

    private function importParishes(string $legacyConnection): int
    {
        $rows = DB::connection($legacyConnection)->table('tb_parroquias')->get();

        foreach ($rows as $legacy) {
            Parish::query()->updateOrCreate(
                ['id' => (int) $legacy->IdParroquia],
                [
                    'deanery_id' => (int) $legacy->Arciprestazgo,
                    'name' => trim((string) $legacy->NombreParroquia),
                    'email' => $this->nullableString($legacy->CorreoParroquia),
                    'description' => $this->nullableString($legacy->DescripcionParroquia),
                    'address' => $this->nullableString($legacy->DireccionParroquia),
                    'phone' => $this->nullableString($legacy->TelefonoParroquia),
                    'web' => $this->nullableString($legacy->WebParroquia),
                ]
            );
        }

        return $rows->count();
    }

    private function importCommunities(string $legacyConnection): int
    {
        $rows = DB::connection($legacyConnection)->table('tb_comunidades')->get();

        foreach ($rows as $legacy) {
            Community::query()->updateOrCreate(
                ['id' => (int) $legacy->IdComunidad],
                [
                    'parish_id' => (int) $legacy->Parroquia,
                    'name' => trim((string) $legacy->NombreComunidad),
                    'legacy_login' => $this->nullableString($legacy->login),
                    'email' => $this->nullableString($legacy->CorreoComunidad),
                    'description' => $this->nullableString($legacy->DescripcionComunidad),
                    'address' => $this->nullableString($legacy->DireccionComunidad),
                    'phone' => $this->nullableString($legacy->TelefonoComunidad),
                ]
            );
        }

        return $rows->count();
    }

    private function importPriestTitles(string $legacyConnection): int
    {
        $rows = DB::connection($legacyConnection)->table('tb_titulos_sacerdotes')->get();

        foreach ($rows as $legacy) {
            PriestTitle::query()->updateOrCreate(
                ['id' => (int) $legacy->IdTitulo],
                [
                    'title' => trim((string) $legacy->Titulo),
                    'description' => $this->nullableString($legacy->Descripcion),
                ]
            );
        }

        return $rows->count();
    }

    private function importParishRoles(string $legacyConnection): int
    {
        $rows = DB::connection($legacyConnection)->table('tb_cargos_parroquia')->get();

        foreach ($rows as $legacy) {
            ParishRole::query()->updateOrCreate(
                ['id' => (int) $legacy->IdCargo],
                [
                    'description' => trim((string) $legacy->Descripcion),
                ]
            );
        }

        return $rows->count();
    }

    private function importPriests(string $legacyConnection): int
    {
        $rows = DB::connection($legacyConnection)->table('tb_sacerdotes')->get();

        foreach ($rows as $legacy) {
            Priest::query()->updateOrCreate(
                ['id' => (int) $legacy->IdSacerdote],
                [
                    'name' => trim((string) $legacy->NombreSacerdote),
                    'priest_title_id' => $legacy->TituloSacerdote ? (int) $legacy->TituloSacerdote : null,
                    'bio' => $this->nullableString($legacy->CurriculoSacerdote),
                    'phone' => $this->nullableString($legacy->TelefonoSacerdote),
                    'email' => $this->nullableString($legacy->CorreoSacerdote),
                ]
            );
        }

        return $rows->count();
    }

    private function importItems(string $legacyConnection): int
    {
        $rows = DB::connection($legacyConnection)->table('tb_articulos')->get();

        foreach ($rows as $legacy) {
            Item::query()->updateOrCreate(
                ['id' => (int) $legacy->IdArticulo],
                [
                    'parish_id' => (int) $legacy->Parroquia,
                    'community_id' => (int) $legacy->Comunidad,
                    'name' => trim((string) $legacy->NombreArticulo),
                    'description' => $this->nullableString($legacy->DescripcionArticulo),
                    'condition' => $legacy->EstadoArticulo ?: 'B',
                    'price' => (int) ($legacy->PrecioArticulo ?? 0),
                    'acquired_at' => $legacy->FechaArticulo,
                    'is_active' => ($legacy->ArticuloActivo ?? 'S') === 'S',
                ]
            );
        }

        return $rows->count();
    }

    private function importRestorations(string $legacyConnection): int
    {
        $rows = DB::connection($legacyConnection)->table('tb_articulos_restauracion')->get();

        foreach ($rows as $legacy) {
            Restoration::query()->updateOrCreate(
                ['id' => (int) $legacy->IdRestauracion],
                [
                    'item_id' => (int) $legacy->Articulo,
                    'restored_at' => $legacy->FechaRestauracion,
                    'restoration_cost' => $legacy->CostoRestauracion !== null ? (int) $legacy->CostoRestauracion : null,
                ]
            );
        }

        return $rows->count();
    }

    private function importParishPriestAssignments(string $legacyConnection): int
    {
        $rows = DB::connection($legacyConnection)->table('tb_sacerdotes_parroquias')->get();

        foreach ($rows as $legacy) {
            ParishPriestAssignment::query()->updateOrCreate(
                [
                    'parish_id' => (int) $legacy->Parroquia,
                    'priest_id' => (int) $legacy->Sacerdote,
                    'parish_role_id' => $legacy->CargoParroquia ? (int) $legacy->CargoParroquia : null,
                ],
                [
                    'is_current' => true,
                ]
            );
        }

        return $rows->count();
    }

    private function importUsers(string $legacyConnection): int
    {
        $users = DB::connection($legacyConnection)
            ->table('sec_users')
            ->select(['login', 'pswd', 'name', 'email', 'active'])
            ->get();

        $groups = DB::connection($legacyConnection)
            ->table('sec_users_groups')
            ->select(['login', 'group_id'])
            ->get()
            ->groupBy('login');

        foreach ($users as $legacyUser) {
            $username = trim((string) $legacyUser->login);

            $user = User::query()->updateOrCreate(
                ['username' => $username],
                [
                    'name' => $this->nullableString($legacyUser->name) ?? $username,
                    'email' => $this->normalizeEmail($legacyUser->email, $username),
                    'password' => Hash::make(Str::password(32)),
                    'is_active' => ($legacyUser->active ?? 'Y') === 'Y',
                    'force_password_reset' => true,
                    'legacy_password_md5' => $this->nullableString($legacyUser->pswd),
                ]
            );

            $roleNames = collect($groups->get($username, collect()))
                ->map(fn ($group) => $this->groupToRole[(int) $group->group_id] ?? null)
                ->filter()
                ->values();

            if ($roleNames->isNotEmpty()) {
                $user->syncRoles($roleNames->all());
            }

            $this->applyUserContext($user);
        }

        return $users->count();
    }

    private function applyUserContext(User $user): void
    {
        $context = [
            'deanery_id' => null,
            'parish_id' => null,
            'community_id' => null,
        ];

        if ($user->isCommunityManager()) {
            $community = Community::query()
                ->with('parish')
                ->where('legacy_login', $user->username)
                ->first();

            if ($community) {
                $context['community_id'] = $community->id;
                $context['parish_id'] = $community->parish_id;
                $context['deanery_id'] = $community->parish?->deanery_id;
            }
        }

        $user->forceFill($context)->save();
    }

    private function importBlobTable(
        string $legacyConnection,
        string $sourceTable,
        string $idColumn,
        string $blobColumn,
        string $targetTable,
        string $targetImageColumn,
        string $folder,
        Filesystem $disk,
        finfo $finfo,
        int &$countRef,
    ): void {
        DB::connection($legacyConnection)
            ->table($sourceTable)
            ->select([$idColumn, $blobColumn])
            ->orderBy($idColumn)
            ->chunk(200, function (Collection $rows) use (
                $idColumn,
                $blobColumn,
                $targetTable,
                $targetImageColumn,
                $folder,
                $disk,
                $finfo,
                &$countRef
            ): void {
                foreach ($rows as $row) {
                    $blob = $row->{$blobColumn};

                    if (! is_string($blob) || $blob === '') {
                        continue;
                    }

                    $relativePath = $this->storeBlob($blob, $folder, (string) $row->{$idColumn}, $disk, $finfo);

                    DB::table($targetTable)
                        ->where('id', (int) $row->{$idColumn})
                        ->update([$targetImageColumn => $relativePath]);

                    $countRef++;
                }
            });
    }

    private function importBlobUsers(string $legacyConnection, Filesystem $disk, finfo $finfo, int &$countRef): void
    {
        DB::connection($legacyConnection)
            ->table('sec_users')
            ->select(['login', 'picture'])
            ->orderBy('login')
            ->chunk(100, function (Collection $rows) use ($disk, $finfo, &$countRef): void {
                foreach ($rows as $row) {
                    $blob = $row->picture;

                    if (! is_string($blob) || $blob === '') {
                        continue;
                    }

                    $relativePath = $this->storeBlob($blob, 'users', (string) $row->login, $disk, $finfo);

                    User::query()
                        ->where('username', (string) $row->login)
                        ->update(['picture_path' => $relativePath]);

                    $countRef++;
                }
            });
    }

    private function storeBlob(string $blob, string $folder, string $id, Filesystem $disk, finfo $finfo): string
    {
        $extension = $this->guessExtension($finfo->buffer($blob));
        $relativePath = "inventory-legacy/{$folder}/{$id}.{$extension}";

        $disk->put($relativePath, $blob);

        return $relativePath;
    }

    private function guessExtension(?string $mime): string
    {
        return match ($mime) {
            'image/jpeg', 'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/bmp' => 'bmp',
            'image/tiff' => 'tiff',
            default => 'bin',
        };
    }

    private function normalizeEmail(mixed $email, string $username): string
    {
        $candidate = $this->nullableString($email);

        if ($candidate && filter_var($candidate, FILTER_VALIDATE_EMAIL)) {
            return $candidate;
        }

        return "{$username}@legacy.local";
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '`'.str_replace('`', '``', $identifier).'`';
    }
}
