<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class InventoryRolesSeeder extends Seeder
{
    public function run(): void
    {
        if (Permission::query()->count() === 0) {
            Artisan::call('shield:generate', [
                '--all' => true,
                '--panel' => 'admin',
                '--no-interaction' => true,
            ]);
        }

        $roleNames = [
            'soporte_tecnico',
            'gestor_diocesis',
            'gestor_parroquia',
            'gestor_comunidad',
        ];

        foreach ($roleNames as $roleName) {
            Role::findOrCreate($roleName, 'web');
        }

        $this->syncRolePermissions();

        $supportUser = User::updateOrCreate(
            ['username' => 'soporte'],
            [
                'name' => 'Soporte Técnico',
                'email' => 'soporte@inventariop.local',
                'password' => Hash::make('Cambiar123!'),
                'is_active' => true,
                'force_password_reset' => true,
                'legacy_password_md5' => null,
            ]
        );

        if (! $supportUser->hasRole('soporte_tecnico')) {
            $supportUser->assignRole('soporte_tecnico');
        }
    }

    private function syncRolePermissions(): void
    {
        $allPermissions = Permission::query()->pluck('name');

        $supportRole = Role::findByName('soporte_tecnico', 'web');
        $supportRole->syncPermissions($allPermissions);

        $gestorDiocesis = Role::findByName('gestor_diocesis', 'web');
        $gestorDiocesis->syncPermissions(
            $this->permissionsFor(
                [
                    'Arciprestazgo',
                    'Parroquia',
                    'Comunidad',
                    'Articulo',
                    'Restauracion',
                    'Sacerdote',
                    'AsignacionParroquiaSacerdote',
                    'TituloSacerdotal',
                    'CargoParroquial',
                    'User',
                ],
                ['ViewAny', 'View', 'Create', 'Update', 'Delete', 'DeleteAny']
            )
        );

        $gestorParroquia = Role::findByName('gestor_parroquia', 'web');
        $gestorParroquia->syncPermissions(
            $this->permissionsFor(
                ['Articulo', 'Restauracion', 'Sacerdote', 'AsignacionParroquiaSacerdote'],
                ['ViewAny', 'View', 'Create', 'Update', 'Delete', 'DeleteAny']
            )
                ->merge($this->permissionsFor(
                    ['Arciprestazgo', 'Parroquia', 'Comunidad', 'TituloSacerdotal', 'CargoParroquial'],
                    ['ViewAny', 'View']
                ))
                ->merge($this->permissionsFor(['User'], ['ViewAny', 'View', 'Update']))
        );

        $gestorComunidad = Role::findByName('gestor_comunidad', 'web');
        $gestorComunidad->syncPermissions(
            $this->permissionsFor(
                ['Articulo', 'Restauracion'],
                ['ViewAny', 'View', 'Create', 'Update', 'Delete', 'DeleteAny']
            )
                ->merge($this->permissionsFor(
                    [
                        'Arciprestazgo',
                        'Parroquia',
                        'Comunidad',
                        'Sacerdote',
                        'AsignacionParroquiaSacerdote',
                        'TituloSacerdotal',
                        'CargoParroquial',
                    ],
                    ['ViewAny', 'View']
                ))
                ->merge($this->permissionsFor(['User'], ['ViewAny', 'View', 'Update']))
        );
    }

    /**
     * @param  list<string>  $entities
     * @param  list<string>  $abilities
     * @return Collection<int, string>
     */
    private function permissionsFor(array $entities, array $abilities): Collection
    {
        $names = collect($entities)
            ->flatMap(fn (string $entity) => collect($abilities)->map(fn (string $ability) => "{$ability}:{$entity}"));

        return Permission::query()
            ->whereIn('name', $names->all())
            ->pluck('name');
    }
}
