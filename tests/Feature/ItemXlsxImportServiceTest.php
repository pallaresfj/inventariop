<?php

use App\Models\Community;
use App\Models\Deanery;
use App\Models\Item;
use App\Models\Parish;
use App\Models\User;
use App\Services\ItemXlsxImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Writer\XLSX\Writer;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('imports valid rows and derives parish from community with null image path', function (): void {
    $fixture = buildItemImportFixture();

    Role::findOrCreate('technical_support', 'web');

    $user = User::factory()->create();
    $user->assignRole('technical_support');

    $xlsxPath = createItemsImportXlsx([
        [$fixture['communityA']->id, 'Articulo 1', 'Descripcion 1', 'B', 1200, '2026-04-01', 1],
        [$fixture['communityA']->id, 'Articulo 2', null, 'R', 0, null, true],
    ]);

    try {
        $result = app(ItemXlsxImportService::class)->import($user, $xlsxPath);

        expect($result['total'])->toBe(2)
            ->and($result['created'])->toBe(2)
            ->and($result['failed'])->toBe(0)
            ->and($result['failure_report_path'])->toBeNull();

        expect(Item::query()->count())->toBe(2)
            ->and(Item::query()->whereNull('image_path')->count())->toBe(2)
            ->and(Item::query()->where('parish_id', $fixture['parishA']->id)->count())->toBe(2);
    } finally {
        @unlink($xlsxPath);
    }
});

it('imports valid rows and reports invalid rows without stopping', function (): void {
    Storage::fake('local');

    $fixture = buildItemImportFixture();

    Role::findOrCreate('technical_support', 'web');

    $user = User::factory()->create();
    $user->assignRole('technical_support');

    $xlsxPath = createItemsImportXlsx([
        [$fixture['communityA']->id, 'Articulo valido', 'Descripcion', 'B', 1000, null, 1],
        [$fixture['communityA']->id, '', 'Sin nombre', 'B', 100, null, 1],
        [999999, 'Comunidad invalida', null, 'B', 100, null, 1],
    ]);

    try {
        $result = app(ItemXlsxImportService::class)->import($user, $xlsxPath);

        expect($result['total'])->toBe(3)
            ->and($result['created'])->toBe(1)
            ->and($result['failed'])->toBe(2)
            ->and($result['failure_report_path'])->not->toBeNull();

        expect(Item::query()->count())->toBe(1);

        expect(Storage::disk('local')->exists((string) $result['failure_report_path']))->toBeTrue();
    } finally {
        @unlink($xlsxPath);
    }
});

it('respects user visibility scope while importing', function (): void {
    $fixture = buildItemImportFixture();

    Role::findOrCreate('parish_manager', 'web');

    $user = User::factory()->create([
        'parish_id' => $fixture['parishA']->id,
    ]);
    $user->assignRole('parish_manager');

    $xlsxPath = createItemsImportXlsx([
        [$fixture['communityA']->id, 'Articulo permitido', null, 'B', 0, null, 1],
        [$fixture['communityB']->id, 'Articulo bloqueado', null, 'B', 0, null, 1],
    ]);

    try {
        $result = app(ItemXlsxImportService::class)->import($user, $xlsxPath);

        expect($result['created'])->toBe(1)
            ->and($result['failed'])->toBe(1)
            ->and(Item::query()->where('community_id', $fixture['communityA']->id)->count())->toBe(1)
            ->and(Item::query()->where('community_id', $fixture['communityB']->id)->count())->toBe(0);
    } finally {
        @unlink($xlsxPath);
    }
});

it('enforces hard limit of 500 rows', function (): void {
    $fixture = buildItemImportFixture();

    Role::findOrCreate('technical_support', 'web');

    $user = User::factory()->create();
    $user->assignRole('technical_support');

    $rows = [];

    for ($index = 1; $index <= 501; $index++) {
        $rows[] = [$fixture['communityA']->id, "Articulo {$index}", null, 'B', 0, null, 1];
    }

    $xlsxPath = createItemsImportXlsx($rows);

    try {
        expect(fn () => app(ItemXlsxImportService::class)->import($user, $xlsxPath))
            ->toThrow(ValidationException::class);

        expect(Item::query()->count())->toBe(0);
    } finally {
        @unlink($xlsxPath);
    }
});

it('generates a community catalog scoped to parish manager visibility', function (): void {
    $fixture = buildItemImportFixture();

    Role::findOrCreate('parish_manager', 'web');

    $user = User::factory()->create([
        'parish_id' => $fixture['parishA']->id,
    ]);
    $user->assignRole('parish_manager');

    $response = app(ItemXlsxImportService::class)->createCommunityCatalogDownloadResponse($user);
    $filePath = $response->getFile()->getPathname();

    try {
        $rows = readXlsxRows($filePath);

        expect($rows[0])->toBe(['community_id', 'community_name', 'parish_name'])
            ->and(count($rows))->toBe(2)
            ->and($rows[1][0])->toBe($fixture['communityA']->id)
            ->and($rows[1][1])->toBe('Comunidad A')
            ->and($rows[1][2])->toBe('Parroquia A');
    } finally {
        @unlink($filePath);
    }
});

it('generates a community catalog scoped to community manager visibility', function (): void {
    $fixture = buildItemImportFixture();

    Role::findOrCreate('community_manager', 'web');

    $user = User::factory()->create([
        'community_id' => $fixture['communityA']->id,
    ]);
    $user->assignRole('community_manager');

    $response = app(ItemXlsxImportService::class)->createCommunityCatalogDownloadResponse($user);
    $filePath = $response->getFile()->getPathname();

    try {
        $rows = readXlsxRows($filePath);
        $catalogIds = array_map(fn (array $row): int => (int) $row[0], array_slice($rows, 1));

        expect($rows[0])->toBe(['community_id', 'community_name', 'parish_name'])
            ->and($catalogIds)->toBe([$fixture['communityA']->id]);
    } finally {
        @unlink($filePath);
    }
});

/**
 * @return array{parishA: Parish, parishB: Parish, communityA: Community, communityB: Community}
 */
function buildItemImportFixture(): array
{
    $deaneryA = Deanery::query()->create([
        'name' => 'Arciprestazgo A',
    ]);

    $deaneryB = Deanery::query()->create([
        'name' => 'Arciprestazgo B',
    ]);

    $parishA = Parish::query()->create([
        'deanery_id' => $deaneryA->id,
        'name' => 'Parroquia A',
    ]);

    $parishB = Parish::query()->create([
        'deanery_id' => $deaneryB->id,
        'name' => 'Parroquia B',
    ]);

    $communityA = Community::query()->create([
        'parish_id' => $parishA->id,
        'name' => 'Comunidad A',
    ]);

    $communityB = Community::query()->create([
        'parish_id' => $parishB->id,
        'name' => 'Comunidad B',
    ]);

    return compact('parishA', 'parishB', 'communityA', 'communityB');
}

/**
 * @param  list<array<int, mixed>>  $rows
 */
function createItemsImportXlsx(array $rows): string
{
    $tempPath = tempnam(sys_get_temp_dir(), 'items-import-');

    if ($tempPath === false) {
        throw new RuntimeException('No fue posible crear un archivo temporal para la plantilla XLSX.');
    }

    $xlsxPath = $tempPath.'.xlsx';
    rename($tempPath, $xlsxPath);

    $writer = new Writer;
    $writer->openToFile($xlsxPath);

    try {
        $writer->addRow(Row::fromValues([
            'community_id',
            'name',
            'description',
            'condition',
            'price',
            'acquired_at',
            'is_active',
        ]));

        foreach ($rows as $row) {
            $writer->addRow(Row::fromValues($row));
        }
    } finally {
        $writer->close();
    }

    return $xlsxPath;
}

/**
 * @return list<list<mixed>>
 */
function readXlsxRows(string $path): array
{
    $reader = new Reader;
    $reader->open($path);

    try {
        $rows = [];

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $rows[] = $row->toArray();
            }

            break;
        }

        return $rows;
    } finally {
        $reader->close();
    }
}
