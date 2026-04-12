<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class InventarioBootstrapAdminCommand extends Command
{
    protected $signature = 'inventario:bootstrap-admin
                            {username=soporte : Usuario administrador}
                            {email=soporte@inventariop.local : Correo administrador}
                            {password=Cambiar123! : Contraseña inicial}
                            {--name=Soporte Técnico : Nombre visible del usuario}';

    protected $description = 'Crea o actualiza el usuario admin soporte_tecnico con acceso inmediato';

    public function handle(): int
    {
        $username = trim((string) $this->argument('username'));
        $email = trim((string) $this->argument('email'));
        $password = (string) $this->argument('password');
        $name = trim((string) $this->option('name'));

        if ($username === '' || $email === '' || $password === '') {
            $this->error('username, email y password no pueden estar vacíos.');

            return self::FAILURE;
        }

        Role::findOrCreate('soporte_tecnico', 'web');

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

        $user->syncRoles(['soporte_tecnico']);

        $this->info('Usuario administrador listo.');
        $this->line(" - Username: {$user->username}");
        $this->line(" - Email: {$user->email}");
        $this->line(' - Rol: soporte_tecnico');
        $this->line(' - force_password_reset: true');

        return self::SUCCESS;
    }
}
