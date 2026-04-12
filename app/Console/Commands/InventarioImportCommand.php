<?php

namespace App\Console\Commands;

use App\Services\LegacyInventoryImporter;
use Illuminate\Console\Command;

class InventarioImportCommand extends Command
{
    protected $signature = 'inventario:import {path=legacy/sql/asyservi_sc_inventariop.sql}';

    protected $description = 'Importa estructura y datos legado de Inventario Parroquial al modelo Laravel';

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

        $this->info("Importando dump legado desde: {$sqlPath}");
        $counts = $importer->importFromSqlDump($sqlPath);

        $this->table(
            ['Entidad', 'Registros importados'],
            collect($counts)
                ->map(fn (int $count, string $entity): array => [$entity, (string) $count])
                ->values()
                ->all()
        );

        $this->info('Importación core completada.');
        $this->line("Siguiente paso recomendado: php artisan inventario:import-media {$path}");

        return self::SUCCESS;
    }
}
