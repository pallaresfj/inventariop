<?php

namespace App\Console\Commands;

use App\Services\LegacyInventoryImporter;
use Illuminate\Console\Command;

class InventoryImportMediaCommand extends Command
{
    protected $signature = 'inventory:import-media {path=legacy/sql/asyservi_sc_inventariop.sql}';

    protected $description = 'Migrate legacy image BLOBs to storage and update paths';

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

        if (! is_link(public_path('storage'))) {
            $this->call('storage:link');
        }

        $this->info("Migrating images from legacy dump: {$sqlPath}");
        $counts = $importer->importMediaFromSqlDump($sqlPath);

        $this->table(
            ['Entity', 'Migrated images'],
            collect($counts)
                ->map(fn (int $count, string $entity): array => [$entity, (string) $count])
                ->values()
                ->all()
        );

        $this->info('Media migration completed.');

        return self::SUCCESS;
    }
}
