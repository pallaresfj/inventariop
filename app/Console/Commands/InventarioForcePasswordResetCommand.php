<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\LegacyInventoryImporter;
use Illuminate\Console\Command;

class InventarioForcePasswordResetCommand extends Command
{
    protected $signature = 'inventario:force-password-reset
                            {user? : ID, username o correo del usuario}
                            {--all : Forzar reset a todos los usuarios}';

    protected $description = 'Fuerza reset de contraseña para bloquear credenciales previas';

    public function handle(LegacyInventoryImporter $importer): int
    {
        $all = (bool) $this->option('all');
        $userArg = $this->argument('user');

        if (! $all && $userArg === null) {
            $this->error('Debes indicar un usuario o usar --all.');

            return self::FAILURE;
        }

        if ($all) {
            $updated = $importer->forcePasswordResetForAll();
            $this->info("Reset forzado aplicado a {$updated} usuarios.");

            return self::SUCCESS;
        }

        $user = User::query()
            ->when(is_numeric((string) $userArg), fn ($query) => $query->whereKey((int) $userArg))
            ->when(! is_numeric((string) $userArg), function ($query) use ($userArg) {
                $query->where('username', (string) $userArg)
                    ->orWhere('email', (string) $userArg);
            })
            ->first();

        if (! $user) {
            $this->error('Usuario no encontrado.');

            return self::FAILURE;
        }

        $importer->forcePasswordReset($user);

        $this->info("Reset forzado aplicado a {$user->username}.");

        return self::SUCCESS;
    }
}
