<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\BuildsInventoryTrends;
use App\Models\Item;
use App\Models\Restoration;
use App\Models\User;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class InventoryHighlights extends StatsOverviewWidget
{
    use BuildsInventoryTrends;

    protected ?string $heading = 'Resumen general';

    protected ?string $description = 'Corte operativo y comparativo mensual';

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

        $visibleItems = (clone $itemsQuery)->count();
        $activeItems = (clone $itemsQuery)->where('is_active', true)->count();
        $activeRatio = $visibleItems > 0
            ? round(($activeItems / $visibleItems) * 100)
            : 0;

        return [
            Stat::make('Articulos visibles', $this->formatCount($visibleItems))
                ->icon(Heroicon::OutlinedArchiveBox)
                ->color('primary')
                ->description($itemsTrend['description'])
                ->descriptionIcon($itemsTrend['icon'])
                ->descriptionColor($itemsTrend['color'])
                ->chart($itemsTrend['chart'])
                ->extraAttributes(['class' => 'inv-stat inv-stat--hero inv-stat--inventory']),
            Stat::make('Articulos activos', $this->formatCount($activeItems))
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success')
                ->description(
                    $visibleItems > 0
                        ? "{$activeRatio}% del inventario visible está activo"
                        : 'Sin articulos visibles en el alcance actual'
                )
                ->descriptionIcon(Heroicon::OutlinedPresentationChartLine)
                ->descriptionColor('info')
                ->extraAttributes(['class' => 'inv-stat inv-stat--hero inv-stat--pastoral']),
            Stat::make('Restauraciones del mes', $this->formatCount($restorationsTrend['current']))
                ->icon(Heroicon::OutlinedWrenchScrewdriver)
                ->color('info')
                ->description($restorationsTrend['description'])
                ->descriptionIcon($restorationsTrend['icon'])
                ->descriptionColor($restorationsTrend['color'])
                ->chart($restorationsTrend['chart'])
                ->extraAttributes(['class' => 'inv-stat inv-stat--hero inv-stat--structure']),
            Stat::make('Costo restauracion del mes', $this->formatCurrency($restorationCostTrend['current']))
                ->icon(Heroicon::OutlinedBanknotes)
                ->color('warning')
                ->description($restorationCostTrend['description'])
                ->descriptionIcon($restorationCostTrend['icon'])
                ->descriptionColor($restorationCostTrend['color'])
                ->chart($restorationCostTrend['chart'])
                ->extraAttributes(['class' => 'inv-stat inv-stat--hero inv-stat--cost']),
        ];
    }
}
