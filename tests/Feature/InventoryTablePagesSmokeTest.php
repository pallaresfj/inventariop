<?php

use App\Models\Community;
use App\Models\Deanery;
use App\Models\Item;
use App\Models\Parish;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('loads inventory table index pages for an authorized user', function (): void {
    $user = User::factory()->create([
        'force_password_reset' => false,
        'is_active' => true,
    ]);

    $technicalSupportRole = Role::findOrCreate('technical_support', 'web');
    $user->assignRole($technicalSupportRole);

    foreach ([
        'ViewAny:Deanery',
        'ViewAny:Parish',
        'ViewAny:Community',
        'ViewAny:Item',
        'ViewAny:Priest',
        'ViewAny:ParishPriestAssignment',
        'ViewAny:PriestTitle',
        'ViewAny:ParishRole',
    ] as $permissionName) {
        Permission::findOrCreate($permissionName, 'web');
        $user->givePermissionTo($permissionName);
    }

    $this->actingAs($user);

    foreach ([
        '/admin/deaneries',
        '/admin/parishes',
        '/admin/communities',
        '/admin/items',
        '/admin/priests',
        '/admin/parish-priest-assignments',
        '/admin/priest-titles',
        '/admin/parish-roles',
    ] as $uri) {
        $this->get($uri)->assertOk();
    }
});

it('loads parent edit pages that render relation managers', function (): void {
    $user = User::factory()->create([
        'force_password_reset' => false,
        'is_active' => true,
    ]);

    $technicalSupportRole = Role::findOrCreate('technical_support', 'web');
    $user->assignRole($technicalSupportRole);

    foreach ([
        'ViewAny:Deanery',
        'View:Deanery',
        'Update:Deanery',
        'ViewAny:Parish',
        'View:Parish',
        'Update:Parish',
        'ViewAny:Community',
        'View:Community',
        'Update:Community',
        'ViewAny:Item',
        'View:Item',
        'Update:Item',
    ] as $permissionName) {
        Permission::findOrCreate($permissionName, 'web');
        $user->givePermissionTo($permissionName);
    }

    $deanery = Deanery::create([
        'name' => 'Deanery Smoke',
    ]);

    $parish = Parish::create([
        'deanery_id' => $deanery->id,
        'name' => 'Parish Smoke',
    ]);

    $community = Community::create([
        'parish_id' => $parish->id,
        'name' => 'Community Smoke',
    ]);

    $item = Item::create([
        'parish_id' => $parish->id,
        'community_id' => $community->id,
        'name' => 'Item Smoke',
        'condition' => 'B',
        'price' => 0,
        'is_active' => true,
    ]);

    $this->actingAs($user);

    $this->get("/admin/deaneries/{$deanery->id}/edit")->assertOk();
    $this->get("/admin/parishes/{$parish->id}/edit")->assertOk();
    $this->get("/admin/communities/{$community->id}/edit")->assertOk();
    $this->get("/admin/items/{$item->id}/edit")->assertOk();
});
