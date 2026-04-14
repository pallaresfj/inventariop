<?php

use App\Filament\Resources\Users\Support\UserLocationScope;
use App\Models\Community;
use App\Models\Deanery;
use App\Models\Parish;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('scopes parish options by selected deanery', function (): void {
    $fixture = buildUserLocationScopeFixture();

    $parishIds = UserLocationScope::scopeParishOptionsQuery(
        Parish::query(),
        $fixture['deaneryA']->id
    )->pluck('id')->all();

    expect($parishIds)->toBe([$fixture['parishA']->id]);
});

it('scopes community options by selected parish', function (): void {
    $fixture = buildUserLocationScopeFixture();

    $communityIds = UserLocationScope::scopeCommunityOptionsQuery(
        Community::query(),
        $fixture['parishA']->id
    )->pluck('id')->all();

    expect($communityIds)->toBe([$fixture['communityA']->id]);
});

it('returns no options when no parent is selected', function (): void {
    buildUserLocationScopeFixture();

    $parishIds = UserLocationScope::scopeParishOptionsQuery(Parish::query(), null)
        ->pluck('id')
        ->all();

    $communityIds = UserLocationScope::scopeCommunityOptionsQuery(Community::query(), null)
        ->pluck('id')
        ->all();

    expect($parishIds)->toBe([])
        ->and($communityIds)->toBe([]);
});

it('requires resetting parish and community when deanery changes', function (): void {
    $fixture = buildUserLocationScopeFixture();

    expect(UserLocationScope::shouldResetParishAndCommunity(
        $fixture['deaneryA']->id,
        $fixture['deaneryA']->id
    ))->toBeFalse()
        ->and(UserLocationScope::shouldResetParishAndCommunity(
            $fixture['deaneryB']->id,
            $fixture['deaneryA']->id
        ))->toBeTrue();
});

it('requires resetting community when parish changes', function (): void {
    $fixture = buildUserLocationScopeFixture();

    expect(UserLocationScope::shouldResetCommunity(
        $fixture['parishA']->id,
        $fixture['parishA']->id
    ))->toBeFalse()
        ->and(UserLocationScope::shouldResetCommunity(
            $fixture['parishB']->id,
            $fixture['parishA']->id
        ))->toBeTrue();
});

/**
 * @return array{
 *     deaneryA: Deanery,
 *     deaneryB: Deanery,
 *     parishA: Parish,
 *     parishB: Parish,
 *     communityA: Community,
 *     communityB: Community
 * }
 */
function buildUserLocationScopeFixture(): array
{
    $deaneryA = Deanery::query()->create([
        'name' => 'Arciprestazgo U-A',
    ]);

    $deaneryB = Deanery::query()->create([
        'name' => 'Arciprestazgo U-B',
    ]);

    $parishA = Parish::query()->create([
        'deanery_id' => $deaneryA->id,
        'name' => 'Parroquia U-A',
    ]);

    $parishB = Parish::query()->create([
        'deanery_id' => $deaneryB->id,
        'name' => 'Parroquia U-B',
    ]);

    $communityA = Community::query()->create([
        'parish_id' => $parishA->id,
        'name' => 'Comunidad U-A',
    ]);

    $communityB = Community::query()->create([
        'parish_id' => $parishB->id,
        'name' => 'Comunidad U-B',
    ]);

    return compact('deaneryA', 'deaneryB', 'parishA', 'parishB', 'communityA', 'communityB');
}
