<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('shows xlsx import actions on items index for users that can create items', function (): void {
    $user = createItemPageUser([
        'ViewAny:Item',
        'Create:Item',
    ]);

    $this->actingAs($user)
        ->get('/admin/items')
        ->assertOk()
        ->assertSee('Descargar catalogo de comunidades')
        ->assertSee('Descargar plantilla XLSX')
        ->assertSee('Importar articulos');
});

it('hides xlsx import actions on items index for users without create permission', function (): void {
    $user = createItemPageUser([
        'ViewAny:Item',
    ]);

    $this->actingAs($user)
        ->get('/admin/items')
        ->assertOk()
        ->assertDontSee('Descargar catalogo de comunidades')
        ->assertDontSee('Descargar plantilla XLSX')
        ->assertDontSee('Importar articulos');
});

/**
 * @param  list<string>  $permissions
 */
function createItemPageUser(array $permissions): User
{
    $user = User::factory()->create([
        'force_password_reset' => false,
        'is_active' => true,
    ]);

    $technicalSupportRole = Role::findOrCreate('technical_support', 'web');
    $user->assignRole($technicalSupportRole);

    foreach ($permissions as $permissionName) {
        Permission::findOrCreate($permissionName, 'web');
        $user->givePermissionTo($permissionName);
    }

    return $user;
}
