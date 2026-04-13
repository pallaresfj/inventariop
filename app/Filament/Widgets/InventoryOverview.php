<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\BuildsInventoryTrends;
use App\Models\Community;
use App\Models\Deanery;
use App\Models\Item;
use App\Models\Parish;
use App\Models\ParishPriestAssignment;
use App\Models\Priest;
use App\Models\Restoration;
use App\Models\User;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class InventoryOverview extends StatsOverviewWidget
{
    use BuildsInventoryTrends;

    protected ?string $heading = 'Indicadores de inventario';

    protected ?string $description = 'Metricas por alcance y rol con comparativo mensual';

    protected int|array|null $columns = [
        'md' => 2,
        'xl' => 4,
    ];

    protected function getStats(): array
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return [];
        }

        return $this->buildStatsFor($user);
    }

    /**
     * @return array<Stat>
     */
    public function buildStatsFor(User $user): array
    {
        $itemsQuery = Item::query()->visibleTo($user);
        $restorationsQuery = Restoration::query()->visibleTo($user);

        $itemsTrend = $this->monthlyCountTrend($itemsQuery, 'created_at');
        $restorationsTrend = $this->monthlyCountTrend($restorationsQuery, 'restored_at');
        $restorationCostTrend = $this->monthlySumTrend($restorationsQuery, 'restored_at', 'restoration_cost');

        $stats = [
            Stat::make('Articulos visibles', $this->formatCount((clone $itemsQuery)->count()))
                ->icon(Heroicon::OutlinedArchiveBox)
                ->color('primary')
                ->description($itemsTrend['description'])
                ->descriptionIcon($itemsTrend['icon'])
                ->descriptionColor($itemsTrend['color'])
                ->chart($itemsTrend['chart'])
                ->extraAttributes(['class' => 'inv-stat inv-stat--inventory']),
            Stat::make('Articulos activos', $this->formatCount((clone $itemsQuery)->where('is_active', true)->count()))
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success')
                ->description('Estado actual del inventario visible')
                ->descriptionIcon(Heroicon::OutlinedCalendarDays)
                ->descriptionColor('info')
                ->extraAttributes(['class' => 'inv-stat inv-stat--pastoral']),
            Stat::make('Restauraciones registradas', $this->formatCount((clone $restorationsQuery)->count()))
                ->icon(Heroicon::OutlinedWrenchScrewdriver)
                ->color('info')
                ->description($restorationsTrend['description'])
                ->descriptionIcon($restorationsTrend['icon'])
                ->descriptionColor($restorationsTrend['color'])
                ->chart($restorationsTrend['chart'])
                ->extraAttributes(['class' => 'inv-stat inv-stat--structure']),
            Stat::make(
                'Costo acumulado de restauracion',
                $this->formatCurrency((float) (clone $restorationsQuery)->sum('restoration_cost'))
            )
                ->icon(Heroicon::OutlinedBanknotes)
                ->color('warning')
                ->description($restorationCostTrend['description'])
                ->descriptionIcon($restorationCostTrend['icon'])
                ->descriptionColor($restorationCostTrend['color'])
                ->chart($restorationCostTrend['chart'])
                ->extraAttributes(['class' => 'inv-stat inv-stat--cost']),
        ];

        if ($user->isTechnicalSupport() || $user->isDioceseManager()) {
            $deaneriesQuery = Deanery::query()->visibleTo($user);
            $parishesQuery = Parish::query()->visibleTo($user);
            $communitiesQuery = Community::query()->visibleTo($user);
            $priestsQuery = Priest::query()->visibleTo($user);

            $deaneriesTrend = $this->monthlyCountTrend($deaneriesQuery, 'created_at');
            $parishesTrend = $this->monthlyCountTrend($parishesQuery, 'created_at');
            $communitiesTrend = $this->monthlyCountTrend($communitiesQuery, 'created_at');
            $priestsTrend = $this->monthlyCountTrend($priestsQuery, 'created_at');

            $stats = [
                ...$stats,
                Stat::make('Arciprestazgos', $this->formatCount((clone $deaneriesQuery)->count()))
                    ->icon(Heroicon::OutlinedBuildingOffice2)
                    ->color('info')
                    ->description($deaneriesTrend['description'])
                    ->descriptionIcon($deaneriesTrend['icon'])
                    ->descriptionColor($deaneriesTrend['color'])
                    ->chart($deaneriesTrend['chart'])
                    ->extraAttributes(['class' => 'inv-stat inv-stat--structure']),
                Stat::make('Parroquias', $this->formatCount((clone $parishesQuery)->count()))
                    ->icon(Heroicon::OutlinedBuildingLibrary)
                    ->color('info')
                    ->description($parishesTrend['description'])
                    ->descriptionIcon($parishesTrend['icon'])
                    ->descriptionColor($parishesTrend['color'])
                    ->chart($parishesTrend['chart'])
                    ->extraAttributes(['class' => 'inv-stat inv-stat--structure']),
                Stat::make('Comunidades', $this->formatCount((clone $communitiesQuery)->count()))
                    ->icon(Heroicon::OutlinedUsers)
                    ->color('info')
                    ->description($communitiesTrend['description'])
                    ->descriptionIcon($communitiesTrend['icon'])
                    ->descriptionColor($communitiesTrend['color'])
                    ->chart($communitiesTrend['chart'])
                    ->extraAttributes(['class' => 'inv-stat inv-stat--structure']),
                Stat::make('Sacerdotes', $this->formatCount((clone $priestsQuery)->count()))
                    ->icon(Heroicon::OutlinedUserGroup)
                    ->color('success')
                    ->description($priestsTrend['description'])
                    ->descriptionIcon($priestsTrend['icon'])
                    ->descriptionColor($priestsTrend['color'])
                    ->chart($priestsTrend['chart'])
                    ->extraAttributes(['class' => 'inv-stat inv-stat--pastoral']),
                Stat::make(
                    'Asignaciones vigentes',
                    $this->formatCount(
                        ParishPriestAssignment::query()->visibleTo($user)->where('is_current', true)->count()
                    )
                )
                    ->icon(Heroicon::OutlinedClipboardDocumentList)
                    ->color('success')
                    ->description('Asignaciones activas al corte actual')
                    ->descriptionIcon(Heroicon::OutlinedCalendarDays)
                    ->descriptionColor('info')
                    ->extraAttributes(['class' => 'inv-stat inv-stat--pastoral']),
            ];
        }

        if ($user->isParishManager()) {
            $parishesQuery = Parish::query()->visibleTo($user);
            $communitiesQuery = Community::query()->visibleTo($user);
            $priestsQuery = Priest::query()->visibleTo($user);

            $parishesTrend = $this->monthlyCountTrend($parishesQuery, 'created_at');
            $communitiesTrend = $this->monthlyCountTrend($communitiesQuery, 'created_at');
            $priestsTrend = $this->monthlyCountTrend($priestsQuery, 'created_at');

            $stats = [
                ...$stats,
                Stat::make('Parroquias visibles', $this->formatCount((clone $parishesQuery)->count()))
                    ->icon(Heroicon::OutlinedBuildingLibrary)
                    ->color('info')
                    ->description($parishesTrend['description'])
                    ->descriptionIcon($parishesTrend['icon'])
                    ->descriptionColor($parishesTrend['color'])
                    ->chart($parishesTrend['chart'])
                    ->extraAttributes(['class' => 'inv-stat inv-stat--structure']),
                Stat::make('Comunidades visibles', $this->formatCount((clone $communitiesQuery)->count()))
                    ->icon(Heroicon::OutlinedUsers)
                    ->color('info')
                    ->description($communitiesTrend['description'])
                    ->descriptionIcon($communitiesTrend['icon'])
                    ->descriptionColor($communitiesTrend['color'])
                    ->chart($communitiesTrend['chart'])
                    ->extraAttributes(['class' => 'inv-stat inv-stat--structure']),
                Stat::make('Sacerdotes visibles', $this->formatCount((clone $priestsQuery)->count()))
                    ->icon(Heroicon::OutlinedUserGroup)
                    ->color('success')
                    ->description($priestsTrend['description'])
                    ->descriptionIcon($priestsTrend['icon'])
                    ->descriptionColor($priestsTrend['color'])
                    ->chart($priestsTrend['chart'])
                    ->extraAttributes(['class' => 'inv-stat inv-stat--pastoral']),
                Stat::make(
                    'Asignaciones vigentes',
                    $this->formatCount(
                        ParishPriestAssignment::query()->visibleTo($user)->where('is_current', true)->count()
                    )
                )
                    ->icon(Heroicon::OutlinedClipboardDocumentList)
                    ->color('success')
                    ->description('Asignaciones activas al corte actual')
                    ->descriptionIcon(Heroicon::OutlinedCalendarDays)
                    ->descriptionColor('info')
                    ->extraAttributes(['class' => 'inv-stat inv-stat--pastoral']),
            ];
        }

        if ($user->isCommunityManager()) {
            $stats = $this->appendVisibleStat(
                $stats,
                'Parroquias visibles',
                Parish::query()->visibleTo($user),
                Heroicon::OutlinedBuildingLibrary,
                'structure'
            );

            $stats = $this->appendVisibleStat(
                $stats,
                'Comunidades visibles',
                Community::query()->visibleTo($user),
                Heroicon::OutlinedUsers,
                'structure'
            );

            $stats = $this->appendVisibleStat(
                $stats,
                'Sacerdotes visibles',
                Priest::query()->visibleTo($user),
                Heroicon::OutlinedUserGroup,
                'pastoral'
            );

            $stats = $this->appendVisibleCurrentAssignmentStat($stats, $user);
        }

        if ($user->isTechnicalSupport()) {
            $rolesTrend = $this->monthlyCountTrend(Role::query(), 'created_at');

            $stats = [
                ...$stats,
                Stat::make('Usuarios activos', $this->formatCount(User::query()->where('is_active', true)->count()))
                    ->icon(Heroicon::OutlinedUserCircle)
                    ->color('danger')
                    ->description('Usuarios habilitados actualmente')
                    ->descriptionIcon(Heroicon::OutlinedCalendarDays)
                    ->descriptionColor('info')
                    ->extraAttributes(['class' => 'inv-stat inv-stat--security']),
                Stat::make('Roles definidos', $this->formatCount(Role::query()->count()))
                    ->icon(Heroicon::OutlinedShieldCheck)
                    ->color('danger')
                    ->description($rolesTrend['description'])
                    ->descriptionIcon($rolesTrend['icon'])
                    ->descriptionColor($rolesTrend['color'])
                    ->chart($rolesTrend['chart'])
                    ->extraAttributes(['class' => 'inv-stat inv-stat--security']),
            ];
        }

        return $stats;
    }

    /**
     * @param  array<Stat>  $stats
     * @return array<Stat>
     */
    private function appendVisibleStat(
        array $stats,
        string $label,
        Builder $query,
        Heroicon $icon,
        string $category
    ): array {
        $count = (clone $query)->count();

        if ($count <= 0) {
            return $stats;
        }

        $trend = $this->monthlyCountTrend($query, 'created_at');

        return [
            ...$stats,
            Stat::make($label, $this->formatCount($count))
                ->icon($icon)
                ->color($category === 'pastoral' ? 'success' : 'info')
                ->description($trend['description'])
                ->descriptionIcon($trend['icon'])
                ->descriptionColor($trend['color'])
                ->chart($trend['chart'])
                ->extraAttributes(['class' => "inv-stat inv-stat--{$category}"]),
        ];
    }

    /**
     * @param  array<Stat>  $stats
     * @return array<Stat>
     */
    private function appendVisibleCurrentAssignmentStat(array $stats, User $user): array
    {
        $query = ParishPriestAssignment::query()->visibleTo($user)->where('is_current', true);
        $count = (clone $query)->count();

        if ($count <= 0) {
            return $stats;
        }

        return [
            ...$stats,
            Stat::make('Asignaciones vigentes', $this->formatCount($count))
                ->icon(Heroicon::OutlinedClipboardDocumentList)
                ->color('success')
                ->description('Asignaciones activas al corte actual')
                ->descriptionIcon(Heroicon::OutlinedCalendarDays)
                ->descriptionColor('info')
                ->extraAttributes(['class' => 'inv-stat inv-stat--pastoral']),
        ];
    }
}
