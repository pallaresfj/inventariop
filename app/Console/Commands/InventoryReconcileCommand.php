<?php

namespace App\Console\Commands;

use App\Services\LegacyInventoryImporter;
use Illuminate\Console\Command;

class InventoryReconcileCommand extends Command
{
    protected $signature = 'inventory:reconcile {path=legacy/sql/asyservi_sc_inventariop.sql} {--strict : Return failure when differences exist}';

    protected $description = 'Reconcile row counts between legacy tables and the new schema';

    public function handle(LegacyInventoryImporter $importer): int
    {
        $path = (string) $this->argument('path');
        $sqlPath = str_starts_with($path, DIRECTORY_SEPARATOR) ? $path : base_path($path);

        $missing = $importer->ensureLegacyArtifacts($sqlPath);

        if ($missing !== []) {
            $this->error('Reconciliation stopped: missing legacy artifacts.');

            foreach ($missing as $item) {
                $this->line(" - {$item}");
            }

            return self::FAILURE;
        }

        $this->info("Reconciling counts from legacy dump: {$sqlPath}");
        $rows = $importer->reconcileFromSqlDump($sqlPath);

        $this->table(
            ['Legacy table', 'New table', 'Legacy', 'New', 'Status'],
            $rows->map(fn (array $row): array => [
                $row['legacy'],
                $row['new'],
                (string) $row['legacy_count'],
                (string) $row['new_count'],
                $row['match'] ? 'OK' : 'DIFF',
            ])->all()
        );

        $differences = $rows->where('match', false)->count();

        if ($differences > 0) {
            $this->warn("Detected {$differences} count differences.");

            return $this->option('strict') ? self::FAILURE : self::SUCCESS;
        }

        $this->info('Count reconciliation completed with no differences.');

        return self::SUCCESS;
    }
}
