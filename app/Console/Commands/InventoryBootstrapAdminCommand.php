<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class InventoryBootstrapAdminCommand extends Command
{
    protected $signature = 'inventory:bootstrap-admin
                            {username=support : Admin username}
                            {email=support@inventariop.local : Admin email}
                            {password=ChangeMe123! : Initial password}
                            {--name=Technical Support : Display name for the user}';

    protected $description = 'Create or update the technical_support admin user with immediate access';

    public function handle(): int
    {
        $username = trim((string) $this->argument('username'));
        $email = trim((string) $this->argument('email'));
        $password = (string) $this->argument('password');
        $name = trim((string) $this->option('name'));

        if ($username === '' || $email === '' || $password === '') {
            $this->error('username, email, and password cannot be empty.');

            return self::FAILURE;
        }

        Role::findOrCreate('technical_support', 'web');

        $user = User::query()->updateOrCreate(
            ['username' => $username],
            [
                'name' => $name === '' ? $username : $name,
                'email' => $email,
                'password' => Hash::make($password),
                'is_active' => true,
                'force_password_reset' => true,
                'legacy_password_md5' => null,
            ]
        );

        $user->syncRoles(['technical_support']);

        $this->info('Administrator user is ready.');
        $this->line(" - Username: {$user->username}");
        $this->line(" - Email: {$user->email}");
        $this->line(' - Role: technical_support');
        $this->line(' - force_password_reset: true');

        return self::SUCCESS;
    }
}
