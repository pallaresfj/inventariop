<?php

namespace App\Console\Commands;

use App\Services\LegacyInventoryImporter;
use Illuminate\Console\Command;

class InventoryImportCommand extends Command
{
    protected $signature = 'inventory:import {path=legacy/sql/asyservi_sc_inventariop.sql}';

    protected $description = 'Import legacy inventory structure and data into the Laravel domain model';

    public function handle(LegacyInventoryImporter $importer): int
    {
        $path = (string) $this->argument('path');
        $sqlPath = str_starts_with($path, DIRECTORY_SEPARATOR) ? $path : base_path($path);

        $missing = $importer->ensureLegacyArtifacts($sqlPath);

        if ($missing !== []) {
            $this->error('Import stopped: missing legacy artifacts.');

            foreach ($missing as $item) {
                $this->line(" - {$item}");
            }

            return self::FAILURE;
        }

        $this->info("Importing legacy dump from: {$sqlPath}");
        $counts = $importer->importFromSqlDump($sqlPath);

        $this->table(
            ['Entity', 'Imported records'],
            collect($counts)
                ->map(fn (int $count, string $entity): array => [$entity, (string) $count])
                ->values()
                ->all()
        );

        $this->info('Core import completed.');
        $this->line("Next recommended step: php artisan inventory:import-media {$path}");

        return self::SUCCESS;
    }
}
