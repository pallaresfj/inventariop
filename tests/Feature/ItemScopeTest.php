<?php

use App\Filament\Resources\Items\Pages\CreateItem;
use App\Filament\Resources\Items\Pages\EditItem;
use App\Filament\Resources\Items\Support\ItemScope;
use App\Models\Community;
use App\Models\Deanery;
use App\Models\Parish;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('filters community options by selected parish', function (): void {
    $fixture = buildItemScopeFixture();

    $allowedCommunityIds = ItemScope::scopeCommunityOptionsQuery(
        Community::query(),
        $fixture['parishA']->id,
    )
        ->pluck('id')
        ->all();

    expect($allowedCommunityIds)
        ->toContain($fixture['communityA']->id)
        ->not->toContain($fixture['communityB']->id);
});

it('flags community reset when parish value changes', function (): void {
    expect(ItemScope::shouldResetCommunity(1, 1))->toBeFalse()
        ->and(ItemScope::shouldResetCommunity(2, 1))->toBeTrue()
        ->and(ItemScope::shouldResetCommunity(null, 1))->toBeTrue()
        ->and(ItemScope::shouldResetCommunity('2', 2))->toBeFalse();
});

it('forces parish id on create for parish manager users', function (): void {
    $fixture = buildItemScopeFixture();

    Role::findOrCreate('parish_manager', 'web');

    $user = User::factory()->create([
        'parish_id' => $fixture['parishA']->id,
    ]);
    $user->assignRole('parish_manager');

    $this->actingAs($user);

    $page = app(CreateItem::class);
    $method = new ReflectionMethod(CreateItem::class, 'mutateFormDataBeforeCreate');
    $method->setAccessible(true);

    $mutatedData = $method->invoke($page, [
        'parish_id' => $fixture['parishB']->id,
        'community_id' => $fixture['communityA']->id,
    ]);

    expect($mutatedData['parish_id'])->toBe($fixture['parishA']->id)
        ->and($mutatedData['community_id'])->toBe($fixture['communityA']->id);
});

it('forces parish and community on edit for community manager users', function (): void {
    $fixture = buildItemScopeFixture();

    Role::findOrCreate('community_manager', 'web');

    $user = User::factory()->create([
        'community_id' => $fixture['communityA']->id,
    ]);
    $user->assignRole('community_manager');

    $this->actingAs($user);

    $page = app(EditItem::class);
    $method = new ReflectionMethod(EditItem::class, 'mutateFormDataBeforeSave');
    $method->setAccessible(true);

    $mutatedData = $method->invoke($page, [
        'parish_id' => $fixture['parishB']->id,
        'community_id' => $fixture['communityB']->id,
    ]);

    expect($mutatedData['parish_id'])->toBe($fixture['parishA']->id)
        ->and($mutatedData['community_id'])->toBe($fixture['communityA']->id);
});

/**
 * @return array{parishA: Parish, parishB: Parish, communityA: Community, communityB: Community}
 */
function buildItemScopeFixture(): array
{
    $deaneryA = Deanery::query()->create([
        'name' => 'Arciprestazgo A',
    ]);

    $deaneryB = Deanery::query()->create([
        'name' => 'Arciprestazgo B',
    ]);

    $parishA = Parish::query()->create([
        'deanery_id' => $deaneryA->id,
        'name' => 'Parroquia A',
    ]);

    $parishB = Parish::query()->create([
        'deanery_id' => $deaneryB->id,
        'name' => 'Parroquia B',
    ]);

    $communityA = Community::query()->create([
        'parish_id' => $parishA->id,
        'name' => 'Comunidad A',
    ]);

    $communityB = Community::query()->create([
        'parish_id' => $parishB->id,
        'name' => 'Comunidad B',
    ]);

    return compact('parishA', 'parishB', 'communityA', 'communityB');
}
