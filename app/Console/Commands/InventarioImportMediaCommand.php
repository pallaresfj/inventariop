<?php

namespace App\Console\Commands;

use App\Services\LegacyInventoryImporter;
use Illuminate\Console\Command;

class InventarioImportMediaCommand extends Command
{
    protected $signature = 'inventario:import-media {path=legacy/sql/asyservi_sc_inventariop.sql}';

    protected $description = 'Migra BLOBs de imágenes legado a storage y actualiza rutas';

    public function handle(LegacyInventoryImporter $importer): int
    {
        $path = (string) $this->argument('path');
        $sqlPath = str_starts_with($path, DIRECTORY_SEPARATOR) ? $path : base_path($path);

        $missing = $importer->ensureLegacyArtifacts($sqlPath);

        if ($missing !== []) {
            $this->error('Proceso detenido: faltan artefactos del legado.');

            foreach ($missing as $item) {
                $this->line(" - {$item}");
            }

            return self::FAILURE;
        }

        if (! is_link(public_path('storage'))) {
            $this->call('storage:link');
        }

        $this->info("Migrando imágenes desde dump legado: {$sqlPath}");
        $counts = $importer->importMediaFromSqlDump($sqlPath);

        $this->table(
            ['Entidad', 'Imágenes migradas'],
            collect($counts)
                ->map(fn (int $count, string $entity): array => [$entity, (string) $count])
                ->values()
                ->all()
        );

        $this->info('Migración de medios completada.');

        return self::SUCCESS;
    }
}
