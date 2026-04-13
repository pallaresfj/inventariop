<?php

use App\Filament\Widgets\InventoryHighlights;
use App\Filament\Widgets\InventoryOverview;
use App\Models\Community;
use App\Models\Deanery;
use App\Models\Item;
use App\Models\Parish;
use App\Models\ParishPriestAssignment;
use App\Models\ParishRole;
use App\Models\Priest;
use App\Models\PriestTitle;
use App\Models\Restoration;
use App\Models\User;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('loads the admin dashboard for all inventory roles', function (): void {
    $context = buildDashboardFixture();

    foreach (['technical', 'diocese', 'parish', 'community'] as $key) {
        $this->actingAs($context[$key])
            ->get('/admin')
            ->assertOk();
    }
});

it('builds four highlight stats for all inventory roles', function (): void {
    $context = buildDashboardFixture();
    $widget = app(InventoryHighlights::class);

    foreach (['technical', 'diocese', 'parish', 'community'] as $key) {
        expect($widget->buildStatsFor($context[$key]))->toHaveCount(4);
    }
});

it('builds dashboard metrics with correct scope and visibility per role', function (): void {
    $context = buildDashboardFixture();
    $widget = app(InventoryOverview::class);

    $technicalStats = statsByLabel($widget, $context['technical']);
    expect($technicalStats)->toMatchArray([
        'Articulos visibles' => '3',
        'Articulos activos' => '2',
        'Restauraciones registradas' => '2',
        'Costo acumulado de restauracion' => '$ 4.000',
        'Arciprestazgos' => '2',
        'Parroquias' => '2',
        'Comunidades' => '2',
        'Sacerdotes' => '2',
        'Asignaciones vigentes' => '2',
        'Usuarios activos' => '4',
        'Roles definidos' => '4',
    ]);

    $dioceseStats = statsByLabel($widget, $context['diocese']);
    expect($dioceseStats)->toMatchArray([
        'Articulos visibles' => '2',
        'Articulos activos' => '1',
        'Restauraciones registradas' => '1',
        'Costo acumulado de restauracion' => '$ 1.000',
        'Arciprestazgos' => '1',
        'Parroquias' => '1',
        'Comunidades' => '1',
        'Sacerdotes' => '1',
        'Asignaciones vigentes' => '1',
    ])->not->toHaveKey('Usuarios activos')
        ->not->toHaveKey('Roles definidos');

    $parishStats = statsByLabel($widget, $context['parish']);
    expect($parishStats)->toMatchArray([
        'Articulos visibles' => '2',
        'Articulos activos' => '1',
        'Restauraciones registradas' => '1',
        'Costo acumulado de restauracion' => '$ 1.000',
        'Parroquias visibles' => '1',
        'Comunidades visibles' => '1',
        'Sacerdotes visibles' => '1',
        'Asignaciones vigentes' => '1',
    ])->not->toHaveKey('Usuarios activos')
        ->not->toHaveKey('Roles definidos');

    $communityStats = statsByLabel($widget, $context['community']);
    expect($communityStats)->toMatchArray([
        'Articulos visibles' => '2',
        'Articulos activos' => '1',
        'Restauraciones registradas' => '1',
        'Costo acumulado de restauracion' => '$ 1.000',
        'Parroquias visibles' => '1',
        'Comunidades visibles' => '1',
    ])->not->toHaveKey('Sacerdotes visibles')
        ->not->toHaveKey('Asignaciones vigentes')
        ->not->toHaveKey('Usuarios activos')
        ->not->toHaveKey('Roles definidos');
});

it('builds monthly trends using calendar-month boundaries', function (): void {
    $context = buildDashboardFixture();

    $previousMonthDate = now()->subMonthNoOverflow()->startOfMonth()->addDays(2)->toDateString();
    $currentMonthDate = now()->startOfMonth()->addDays(5)->toDateString();

    $restorations = Restoration::query()->orderBy('id')->get();
    $restorations[0]->update([
        'restored_at' => $previousMonthDate,
        'restoration_cost' => 3500,
    ]);
    $restorations[1]->update([
        'restored_at' => $currentMonthDate,
        'restoration_cost' => 1000,
    ]);

    $widget = app(InventoryOverview::class);
    $stats = collect($widget->buildStatsFor($context['technical']))
        ->keyBy(fn (Stat $stat): string => (string) $stat->getLabel());

    /** @var Stat $restorationsStat */
    $restorationsStat = $stats->get('Restauraciones registradas');
    /** @var Stat $costStat */
    $costStat = $stats->get('Costo acumulado de restauracion');

    expect($restorationsStat->getDescriptionIcon())->toBe(Heroicon::OutlinedMinus)
        ->and(array_values($restorationsStat->getChart()))->toBe([1, 1])
        ->and((string) $restorationsStat->getDescription())->toContain('mes anterior');

    expect($costStat->getDescriptionIcon())->toBe(Heroicon::OutlinedArrowTrendingDown)
        ->and((string) $costStat->getDescription())->toContain('mes anterior')
        ->and(array_values($costStat->getChart())[0])->toBeGreaterThan(array_values($costStat->getChart())[1]);
});

/**
 * @return array{
 *     technical: User,
 *     diocese: User,
 *     parish: User,
 *     community: User
 * }
 */
function buildDashboardFixture(): array
{
    foreach ([
        'technical_support',
        'diocese_manager',
        'parish_manager',
        'community_manager',
    ] as $roleName) {
        Role::findOrCreate($roleName, 'web');
    }

    $priestTitle = PriestTitle::query()->create([
        'title' => 'Pbro.',
    ]);

    $priestA = Priest::query()->create([
        'name' => 'Sacerdote A',
        'priest_title_id' => $priestTitle->id,
    ]);

    $priestB = Priest::query()->create([
        'name' => 'Sacerdote B',
        'priest_title_id' => $priestTitle->id,
    ]);

    $deaneryA = Deanery::query()->create([
        'name' => 'Arciprestazgo A',
        'archpriest_id' => $priestA->id,
    ]);

    $deaneryB = Deanery::query()->create([
        'name' => 'Arciprestazgo B',
        'archpriest_id' => $priestB->id,
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

    $itemA1 = Item::query()->create([
        'parish_id' => $parishA->id,
        'community_id' => $communityA->id,
        'name' => 'Articulo A1',
        'condition' => 'B',
        'price' => 10000,
        'is_active' => true,
    ]);

    Item::query()->create([
        'parish_id' => $parishA->id,
        'community_id' => $communityA->id,
        'name' => 'Articulo A2',
        'condition' => 'M',
        'price' => 5000,
        'is_active' => false,
    ]);

    $itemB1 = Item::query()->create([
        'parish_id' => $parishB->id,
        'community_id' => $communityB->id,
        'name' => 'Articulo B1',
        'condition' => 'R',
        'price' => 12000,
        'is_active' => true,
    ]);

    Restoration::query()->create([
        'item_id' => $itemA1->id,
        'restored_at' => now()->toDateString(),
        'restoration_cost' => 1000,
    ]);

    Restoration::query()->create([
        'item_id' => $itemB1->id,
        'restored_at' => now()->toDateString(),
        'restoration_cost' => 3000,
    ]);

    $parishRole = ParishRole::query()->create([
        'description' => 'Parroco',
    ]);

    ParishPriestAssignment::query()->create([
        'parish_id' => $parishA->id,
        'priest_id' => $priestA->id,
        'parish_role_id' => $parishRole->id,
        'is_current' => true,
    ]);

    ParishPriestAssignment::query()->create([
        'parish_id' => $parishB->id,
        'priest_id' => $priestB->id,
        'parish_role_id' => $parishRole->id,
        'is_current' => true,
    ]);

    $technical = User::factory()->create([
        'username' => 'technical',
        'email' => 'technical@example.test',
    ]);
    $technical->assignRole('technical_support');

    $diocese = User::factory()->create([
        'username' => 'diocese',
        'email' => 'diocese@example.test',
        'deanery_id' => $deaneryA->id,
    ]);
    $diocese->assignRole('diocese_manager');

    $parish = User::factory()->create([
        'username' => 'parish',
        'email' => 'parish@example.test',
        'deanery_id' => $deaneryA->id,
        'parish_id' => $parishA->id,
    ]);
    $parish->assignRole('parish_manager');

    $community = User::factory()->create([
        'username' => 'community',
        'email' => 'community@example.test',
        'community_id' => $communityA->id,
    ]);
    $community->assignRole('community_manager');

    return compact('technical', 'diocese', 'parish', 'community');
}

/**
 * @return array<string, string>
 */
function statsByLabel(InventoryOverview $widget, User $user): array
{
    return collect($widget->buildStatsFor($user))
        ->mapWithKeys(fn (Stat $stat): array => [(string) $stat->getLabel() => (string) $stat->getValue()])
        ->all();
}
