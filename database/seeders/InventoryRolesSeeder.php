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
            'technical_support',
            'diocese_manager',
            'parish_manager',
            'community_manager',
        ];

        foreach ($roleNames as $roleName) {
            Role::findOrCreate($roleName, 'web');
        }

        $this->syncRolePermissions();

        $supportUser = User::updateOrCreate(
            ['username' => 'soporte'],
            [
                'name' => 'Soporte Técnico',
                'email' => 'pallaresfj@asyservicios.com',
                'password' => Hash::make('Cambiar123!'),
                'is_active' => true,
                'force_password_reset' => true,
                'legacy_password_md5' => null,
            ]
        );

        if (! $supportUser->hasRole('technical_support')) {
            $supportUser->assignRole('technical_support');
        }
    }

    private function syncRolePermissions(): void
    {
        $allPermissions = Permission::query()->pluck('name');

        $supportRole = Role::findByName('technical_support', 'web');
        $supportRole->syncPermissions($allPermissions);

        $dioceseManagerRole = Role::findByName('diocese_manager', 'web');
        $dioceseManagerRole->syncPermissions(
            $this->permissionsFor(
                [
                    'Deanery',
                    'Parish',
                    'Community',
                    'Item',
                    'Restoration',
                    'Priest',
                    'ParishPriestAssignment',
                    'PriestTitle',
                    'ParishRole',
                    'User',
                ],
                ['ViewAny', 'View', 'Create', 'Update', 'Delete', 'DeleteAny']
            )
        );

        $parishManagerRole = Role::findByName('parish_manager', 'web');
        $parishManagerRole->syncPermissions(
            $this->permissionsFor(
                ['Item', 'Restoration', 'Priest', 'ParishPriestAssignment'],
                ['ViewAny', 'View', 'Create', 'Update', 'Delete', 'DeleteAny']
            )
                ->merge($this->permissionsFor(
                    ['Deanery', 'Parish', 'Community', 'PriestTitle', 'ParishRole'],
                    ['ViewAny', 'View']
                ))
                ->merge($this->permissionsFor(['User'], ['ViewAny', 'View', 'Update']))
        );

        $communityManagerRole = Role::findByName('community_manager', 'web');
        $communityManagerRole->syncPermissions(
            $this->permissionsFor(
                ['Item', 'Restoration'],
                ['ViewAny', 'View', 'Create', 'Update', 'Delete', 'DeleteAny']
            )
                ->merge($this->permissionsFor(
                    [
                        'Deanery',
                        'Parish',
                        'Community',
                        'Priest',
                        'ParishPriestAssignment',
                        'PriestTitle',
                        'ParishRole',
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
