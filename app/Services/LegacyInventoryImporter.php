<?php

namespace App\Services;

use App\Models\Arciprestazgo;
use App\Models\Articulo;
use App\Models\AsignacionParroquiaSacerdote;
use App\Models\CargoParroquial;
use App\Models\Comunidad;
use App\Models\Parroquia;
use App\Models\Restauracion;
use App\Models\Sacerdote;
use App\Models\TituloSacerdotal;
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
        1 => 'soporte_tecnico',
        2 => 'gestor_diocesis',
        3 => 'gestor_parroquia',
        4 => 'gestor_comunidad',
    ];

    private string $legacyConnectionName = 'legacy_import_tmp';

    public function ensureLegacyArtifacts(string $sqlPath): array
    {
        $missing = [];

        if (! is_file($sqlPath)) {
            $missing[] = "Falta SQL legado: {$sqlPath}";
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
     * @param  callable(string): TReturn  $callback
     * @return TReturn
     */
    private function withTemporaryLegacyDatabase(string $sqlPath, callable $callback)
    {
        $sql = file_get_contents($sqlPath);

        if ($sql === false) {
            throw new \RuntimeException("No fue posible leer el archivo SQL: {$sqlPath}");
        }

        $defaultConnection = (string) config('database.default');
        $baseConfig = config("database.connections.{$defaultConnection}");

        if (! is_array($baseConfig)) {
            throw new \RuntimeException('No se encontró la configuración de la conexión por defecto.');
        }

        $driver = (string) ($baseConfig['driver'] ?? '');

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            throw new \RuntimeException('La importación temporal requiere MySQL o MariaDB como conexión por defecto.');
        }

        $tempDatabase = 'legacy_tmp_' . strtolower(Str::random(10));
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
                'titulos_sacerdotales' => $this->importTitulosSacerdotales($legacyConnection),
                'cargos_parroquiales' => $this->importCargosParroquiales($legacyConnection),
                'sacerdotes' => $this->importSacerdotes($legacyConnection),
                'arciprestazgos' => $this->importArciprestazgos($legacyConnection),
                'parroquias' => $this->importParroquias($legacyConnection),
                'comunidades' => $this->importComunidades($legacyConnection),
                'articulos' => $this->importArticulos($legacyConnection),
                'restauraciones' => $this->importRestauraciones($legacyConnection),
                'asignaciones' => $this->importAsignaciones($legacyConnection),
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
            'arciprestazgos' => 0,
            'parroquias' => 0,
            'comunidades' => 0,
            'articulos' => 0,
            'restauraciones' => 0,
            'sacerdotes' => 0,
            'users' => 0,
        ];

        $this->importBlobTable(
            legacyConnection: $legacyConnection,
            sourceTable: 'tb_arciprestazgos',
            idColumn: 'IdArciprestazgo',
            blobColumn: 'ImagenArciprestazgo',
            targetTable: 'arciprestazgos',
            targetImageColumn: 'imagen_path',
            folder: 'arciprestazgos',
            disk: $disk,
            finfo: $finfo,
            countRef: $counts['arciprestazgos']
        );

        $this->importBlobTable(
            legacyConnection: $legacyConnection,
            sourceTable: 'tb_parroquias',
            idColumn: 'IdParroquia',
            blobColumn: 'ImagenParroquia',
            targetTable: 'parroquias',
            targetImageColumn: 'imagen_path',
            folder: 'parroquias',
            disk: $disk,
            finfo: $finfo,
            countRef: $counts['parroquias']
        );

        $this->importBlobTable(
            legacyConnection: $legacyConnection,
            sourceTable: 'tb_comunidades',
            idColumn: 'IdComunidad',
            blobColumn: 'ImagenComunidad',
            targetTable: 'comunidades',
            targetImageColumn: 'imagen_path',
            folder: 'comunidades',
            disk: $disk,
            finfo: $finfo,
            countRef: $counts['comunidades']
        );

        $this->importBlobTable(
            legacyConnection: $legacyConnection,
            sourceTable: 'tb_articulos',
            idColumn: 'IdArticulo',
            blobColumn: 'ImagenArticulo',
            targetTable: 'articulos',
            targetImageColumn: 'imagen_path',
            folder: 'articulos',
            disk: $disk,
            finfo: $finfo,
            countRef: $counts['articulos']
        );

        $this->importBlobTable(
            legacyConnection: $legacyConnection,
            sourceTable: 'tb_articulos_restauracion',
            idColumn: 'IdRestauracion',
            blobColumn: 'ImagenRestauracion',
            targetTable: 'restauraciones',
            targetImageColumn: 'imagen_path',
            folder: 'restauraciones',
            disk: $disk,
            finfo: $finfo,
            countRef: $counts['restauraciones']
        );

        $this->importBlobTable(
            legacyConnection: $legacyConnection,
            sourceTable: 'tb_sacerdotes',
            idColumn: 'IdSacerdote',
            blobColumn: 'ImagenSacerdote',
            targetTable: 'sacerdotes',
            targetImageColumn: 'imagen_path',
            folder: 'sacerdotes',
            disk: $disk,
            finfo: $finfo,
            countRef: $counts['sacerdotes']
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
            ['legacy' => 'tb_arciprestazgos', 'new' => 'arciprestazgos'],
            ['legacy' => 'tb_parroquias', 'new' => 'parroquias'],
            ['legacy' => 'tb_comunidades', 'new' => 'comunidades'],
            ['legacy' => 'tb_articulos', 'new' => 'articulos'],
            ['legacy' => 'tb_articulos_restauracion', 'new' => 'restauraciones'],
            ['legacy' => 'tb_titulos_sacerdotes', 'new' => 'titulos_sacerdotales'],
            ['legacy' => 'tb_sacerdotes', 'new' => 'sacerdotes'],
            ['legacy' => 'tb_cargos_parroquia', 'new' => 'cargos_parroquiales'],
            ['legacy' => 'tb_sacerdotes_parroquias', 'new' => 'asignacion_parroquia_sacerdotes'],
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

        AsignacionParroquiaSacerdote::query()->truncate();
        Restauracion::query()->truncate();
        Articulo::query()->truncate();
        Comunidad::query()->truncate();
        Parroquia::query()->truncate();
        Arciprestazgo::query()->truncate();
        Sacerdote::query()->truncate();
        CargoParroquial::query()->truncate();
        TituloSacerdotal::query()->truncate();
        User::query()->truncate();
    }

    private function importArciprestazgos(string $legacyConnection): int
    {
        $rows = DB::connection($legacyConnection)->table('tb_arciprestazgos')->get();

        foreach ($rows as $legacy) {
            Arciprestazgo::query()->updateOrCreate(
                ['id' => (int) $legacy->IdArciprestazgo],
                [
                    'nombre' => trim((string) $legacy->NombreArciprestazgo),
                    'correo' => $this->nullableString($legacy->CorreoArciprestazgo),
                    'descripcion' => $this->nullableString($legacy->DescripcionArciprestazgo),
                    'arcipestre_id' => $legacy->Arcipestre ? (int) $legacy->Arcipestre : null,
                ]
            );
        }

        return $rows->count();
    }

    private function importParroquias(string $legacyConnection): int
    {
        $rows = DB::connection($legacyConnection)->table('tb_parroquias')->get();

        foreach ($rows as $legacy) {
            Parroquia::query()->updateOrCreate(
                ['id' => (int) $legacy->IdParroquia],
                [
                    'arciprestazgo_id' => (int) $legacy->Arciprestazgo,
                    'nombre' => trim((string) $legacy->NombreParroquia),
                    'legacy_login' => $this->nullableString($legacy->login),
                    'correo' => $this->nullableString($legacy->CorreoParroquia),
                    'descripcion' => $this->nullableString($legacy->DescripcionParroquia),
                    'direccion' => $this->nullableString($legacy->DireccionParroquia),
                    'telefono' => $this->nullableString($legacy->TelefonoParroquia),
                    'web' => $this->nullableString($legacy->WebParroquia),
                ]
            );
        }

        return $rows->count();
    }

    private function importComunidades(string $legacyConnection): int
    {
        $rows = DB::connection($legacyConnection)->table('tb_comunidades')->get();

        foreach ($rows as $legacy) {
            Comunidad::query()->updateOrCreate(
                ['id' => (int) $legacy->IdComunidad],
                [
                    'parroquia_id' => (int) $legacy->Parroquia,
                    'nombre' => trim((string) $legacy->NombreComunidad),
                    'legacy_login' => $this->nullableString($legacy->login),
                    'correo' => $this->nullableString($legacy->CorreoComunidad),
                    'descripcion' => $this->nullableString($legacy->DescripcionComunidad),
                    'direccion' => $this->nullableString($legacy->DireccionComunidad),
                    'telefono' => $this->nullableString($legacy->TelefonoComunidad),
                ]
            );
        }

        return $rows->count();
    }

    private function importTitulosSacerdotales(string $legacyConnection): int
    {
        $rows = DB::connection($legacyConnection)->table('tb_titulos_sacerdotes')->get();

        foreach ($rows as $legacy) {
            TituloSacerdotal::query()->updateOrCreate(
                ['id' => (int) $legacy->IdTitulo],
                [
                    'titulo' => trim((string) $legacy->Titulo),
                    'descripcion' => $this->nullableString($legacy->Descripcion),
                ]
            );
        }

        return $rows->count();
    }

    private function importCargosParroquiales(string $legacyConnection): int
    {
        $rows = DB::connection($legacyConnection)->table('tb_cargos_parroquia')->get();

        foreach ($rows as $legacy) {
            CargoParroquial::query()->updateOrCreate(
                ['id' => (int) $legacy->IdCargo],
                [
                    'descripcion' => trim((string) $legacy->Descripcion),
                ]
            );
        }

        return $rows->count();
    }

    private function importSacerdotes(string $legacyConnection): int
    {
        $rows = DB::connection($legacyConnection)->table('tb_sacerdotes')->get();

        foreach ($rows as $legacy) {
            Sacerdote::query()->updateOrCreate(
                ['id' => (int) $legacy->IdSacerdote],
                [
                    'nombre' => trim((string) $legacy->NombreSacerdote),
                    'titulo_sacerdotal_id' => $legacy->TituloSacerdote ? (int) $legacy->TituloSacerdote : null,
                    'curriculo' => $this->nullableString($legacy->CurriculoSacerdote),
                    'telefono' => $this->nullableString($legacy->TelefonoSacerdote),
                    'correo' => $this->nullableString($legacy->CorreoSacerdote),
                ]
            );
        }

        return $rows->count();
    }

    private function importArticulos(string $legacyConnection): int
    {
        $rows = DB::connection($legacyConnection)->table('tb_articulos')->get();

        foreach ($rows as $legacy) {
            Articulo::query()->updateOrCreate(
                ['id' => (int) $legacy->IdArticulo],
                [
                    'parroquia_id' => (int) $legacy->Parroquia,
                    'comunidad_id' => (int) $legacy->Comunidad,
                    'nombre' => trim((string) $legacy->NombreArticulo),
                    'descripcion' => $this->nullableString($legacy->DescripcionArticulo),
                    'estado' => $legacy->EstadoArticulo ?: 'B',
                    'precio' => (int) ($legacy->PrecioArticulo ?? 0),
                    'fecha_adquisicion' => $legacy->FechaArticulo,
                    'activo' => ($legacy->ArticuloActivo ?? 'S') === 'S',
                ]
            );
        }

        return $rows->count();
    }

    private function importRestauraciones(string $legacyConnection): int
    {
        $rows = DB::connection($legacyConnection)->table('tb_articulos_restauracion')->get();

        foreach ($rows as $legacy) {
            Restauracion::query()->updateOrCreate(
                ['id' => (int) $legacy->IdRestauracion],
                [
                    'articulo_id' => (int) $legacy->Articulo,
                    'fecha_restauracion' => $legacy->FechaRestauracion,
                    'costo_restauracion' => $legacy->CostoRestauracion !== null ? (int) $legacy->CostoRestauracion : null,
                ]
            );
        }

        return $rows->count();
    }

    private function importAsignaciones(string $legacyConnection): int
    {
        $rows = DB::connection($legacyConnection)->table('tb_sacerdotes_parroquias')->get();

        foreach ($rows as $legacy) {
            AsignacionParroquiaSacerdote::query()->updateOrCreate(
                [
                    'parroquia_id' => (int) $legacy->Parroquia,
                    'sacerdote_id' => (int) $legacy->Sacerdote,
                    'cargo_parroquial_id' => $legacy->CargoParroquia ? (int) $legacy->CargoParroquia : null,
                ],
                [
                    'vigente' => true,
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
            'arciprestazgo_id' => null,
            'parroquia_id' => null,
            'comunidad_id' => null,
        ];

        if ($user->isGestorParroquia()) {
            $parroquia = Parroquia::query()->where('legacy_login', $user->username)->first();

            if ($parroquia) {
                $context['parroquia_id'] = $parroquia->id;
                $context['arciprestazgo_id'] = $parroquia->arciprestazgo_id;
            }
        }

        if ($user->isGestorComunidad()) {
            $comunidad = Comunidad::query()
                ->with('parroquia')
                ->where('legacy_login', $user->username)
                ->first();

            if ($comunidad) {
                $context['comunidad_id'] = $comunidad->id;
                $context['parroquia_id'] = $comunidad->parroquia_id;
                $context['arciprestazgo_id'] = $comunidad->parroquia?->arciprestazgo_id;
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
        $relativePath = "inventario-legacy/{$folder}/{$id}.{$extension}";

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
        return '`' . str_replace('`', '``', $identifier) . '`';
    }
}
