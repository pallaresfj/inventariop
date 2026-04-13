<?php

use App\Filament\Resources\Communities\Pages\CreateCommunity;
use App\Filament\Resources\Communities\Pages\EditCommunity;
use App\Filament\Resources\Communities\Support\CommunityScope;
use App\Models\Deanery;
use App\Models\Parish;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('locks parish field and resolves current parish for parish manager', function (): void {
    $fixture = buildCommunityScopeFixture();

    Role::findOrCreate('parish_manager', 'web');

    $user = User::factory()->create([
        'parish_id' => $fixture['parishA']->id,
    ]);
    $user->assignRole('parish_manager');

    $this->actingAs($user);

    expect(CommunityScope::shouldLockParish())->toBeTrue()
        ->and(CommunityScope::resolveScopedParishId())->toBe($fixture['parishA']->id);
});

it('forces parish id on create for parish manager users', function (): void {
    $fixture = buildCommunityScopeFixture();

    Role::findOrCreate('parish_manager', 'web');

    $user = User::factory()->create([
        'parish_id' => $fixture['parishA']->id,
    ]);
    $user->assignRole('parish_manager');

    $this->actingAs($user);

    $page = app(CreateCommunity::class);
    $method = new ReflectionMethod(CreateCommunity::class, 'mutateFormDataBeforeCreate');
    $method->setAccessible(true);

    $mutatedData = $method->invoke($page, [
        'parish_id' => $fixture['parishB']->id,
    ]);

    expect($mutatedData['parish_id'])->toBe($fixture['parishA']->id);
});

it('forces parish id on edit for parish manager users', function (): void {
    $fixture = buildCommunityScopeFixture();

    Role::findOrCreate('parish_manager', 'web');

    $user = User::factory()->create([
        'parish_id' => $fixture['parishA']->id,
    ]);
    $user->assignRole('parish_manager');

    $this->actingAs($user);

    $page = app(EditCommunity::class);
    $method = new ReflectionMethod(EditCommunity::class, 'mutateFormDataBeforeSave');
    $method->setAccessible(true);

    $mutatedData = $method->invoke($page, [
        'parish_id' => $fixture['parishB']->id,
    ]);

    expect($mutatedData['parish_id'])->toBe($fixture['parishA']->id);
});

/**
 * @return array{parishA: Parish, parishB: Parish}
 */
function buildCommunityScopeFixture(): array
{
    $deaneryA = Deanery::query()->create([
        'name' => 'Arciprestazgo C-A',
    ]);

    $deaneryB = Deanery::query()->create([
        'name' => 'Arciprestazgo C-B',
    ]);

    $parishA = Parish::query()->create([
        'deanery_id' => $deaneryA->id,
        'name' => 'Parroquia C-A',
    ]);

    $parishB = Parish::query()->create([
        'deanery_id' => $deaneryB->id,
        'name' => 'Parroquia C-B',
    ]);

    return compact('parishA', 'parishB');
}
