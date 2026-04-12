<?php

namespace App\Console\Commands;

use App\Services\LegacyInventoryImporter;
use Illuminate\Console\Command;

class InventarioReconcileCommand extends Command
{
    protected $signature = 'inventario:reconcile {path=legacy/sql/asyservi_sc_inventariop.sql} {--strict : Retorna error si hay diferencias}';

    protected $description = 'Conciliar conteos entre tablas legado y el nuevo modelo';

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

        $this->info("Conciliando conteos desde dump legado: {$sqlPath}");
        $rows = $importer->reconcileFromSqlDump($sqlPath);

        $this->table(
            ['Tabla legado', 'Tabla nueva', 'Legado', 'Nueva', 'Estado'],
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
            $this->warn("Se detectaron {$differences} diferencias de conteo.");

            return $this->option('strict') ? self::FAILURE : self::SUCCESS;
        }

        $this->info('Conciliación de conteos sin diferencias.');

        return self::SUCCESS;
    }
}
