<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\LegacyInventoryImporter;
use Illuminate\Console\Command;

class InventoryForcePasswordResetCommand extends Command
{
    protected $signature = 'inventory:force-password-reset
                            {user? : User ID, username, or email}
                            {--all : Force reset for all users}';

    protected $description = 'Force password reset to invalidate previous credentials';

    public function handle(LegacyInventoryImporter $importer): int
    {
        $all = (bool) $this->option('all');
        $userArg = $this->argument('user');

        if (! $all && $userArg === null) {
            $this->error('You must provide a user or use --all.');

            return self::FAILURE;
        }

        if ($all) {
            $updated = $importer->forcePasswordResetForAll();
            $this->info("Forced password reset applied to {$updated} users.");

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
            $this->error('User not found.');

            return self::FAILURE;
        }

        $importer->forcePasswordReset($user);

        $this->info("Forced password reset applied to {$user->username}.");

        return self::SUCCESS;
    }
}
