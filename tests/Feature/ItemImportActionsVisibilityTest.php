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
    ], role: 'technical_support');

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
    ], role: 'technical_support');

    $this->actingAs($user)
        ->get('/admin/items')
        ->assertOk()
        ->assertDontSee('Descargar catalogo de comunidades')
        ->assertDontSee('Descargar plantilla XLSX')
        ->assertDontSee('Importar articulos');
});

it('hides xlsx import actions for non technical support roles even with create permission', function (): void {
    $user = createItemPageUser([
        'ViewAny:Item',
        'Create:Item',
    ], role: 'diocese_manager');

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
function createItemPageUser(array $permissions, string $role): User
{
    $user = User::factory()->create([
        'force_password_reset' => false,
        'is_active' => true,
    ]);

    $assignedRole = Role::findOrCreate($role, 'web');
    $user->assignRole($assignedRole);

    foreach ($permissions as $permissionName) {
        Permission::findOrCreate($permissionName, 'web');
        $user->givePermissionTo($permissionName);
    }

    return $user;
}
