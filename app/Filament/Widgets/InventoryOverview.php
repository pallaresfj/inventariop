<?php

namespace App\Filament\Widgets;

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
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class InventoryOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Indicadores de inventario';

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

        $stats = [
            Stat::make('Articulos visibles', $this->formatCount((clone $itemsQuery)->count()))
                ->icon(Heroicon::OutlinedArchiveBox),
            Stat::make('Articulos activos', $this->formatCount((clone $itemsQuery)->where('is_active', true)->count()))
                ->icon(Heroicon::OutlinedCheckCircle),
            Stat::make('Restauraciones registradas', $this->formatCount((clone $restorationsQuery)->count()))
                ->icon(Heroicon::OutlinedWrenchScrewdriver),
            Stat::make(
                'Costo acumulado de restauracion',
                $this->formatCurrency((float) (clone $restorationsQuery)->sum('restoration_cost'))
            )->icon(Heroicon::OutlinedBanknotes),
        ];

        if ($user->isTechnicalSupport() || $user->isDioceseManager()) {
            $stats = [
                ...$stats,
                Stat::make('Arciprestazgos', $this->formatCount(Deanery::query()->visibleTo($user)->count()))
                    ->icon(Heroicon::OutlinedBuildingOffice2),
                Stat::make('Parroquias', $this->formatCount(Parish::query()->visibleTo($user)->count()))
                    ->icon(Heroicon::OutlinedBuildingLibrary),
                Stat::make('Comunidades', $this->formatCount(Community::query()->visibleTo($user)->count()))
                    ->icon(Heroicon::OutlinedUsers),
                Stat::make('Sacerdotes', $this->formatCount(Priest::query()->visibleTo($user)->count()))
                    ->icon(Heroicon::OutlinedUserGroup),
                Stat::make(
                    'Asignaciones vigentes',
                    $this->formatCount(
                        ParishPriestAssignment::query()->visibleTo($user)->where('is_current', true)->count()
                    )
                )->icon(Heroicon::OutlinedClipboardDocumentList),
            ];
        }

        if ($user->isParishManager()) {
            $stats = [
                ...$stats,
                Stat::make('Parroquias visibles', $this->formatCount(Parish::query()->visibleTo($user)->count()))
                    ->icon(Heroicon::OutlinedBuildingLibrary),
                Stat::make('Comunidades visibles', $this->formatCount(Community::query()->visibleTo($user)->count()))
                    ->icon(Heroicon::OutlinedUsers),
                Stat::make('Sacerdotes visibles', $this->formatCount(Priest::query()->visibleTo($user)->count()))
                    ->icon(Heroicon::OutlinedUserGroup),
                Stat::make(
                    'Asignaciones vigentes',
                    $this->formatCount(
                        ParishPriestAssignment::query()->visibleTo($user)->where('is_current', true)->count()
                    )
                )->icon(Heroicon::OutlinedClipboardDocumentList),
            ];
        }

        if ($user->isCommunityManager()) {
            $stats = $this->appendVisibleStat(
                $stats,
                'Parroquias visibles',
                Parish::query()->visibleTo($user)->count(),
                Heroicon::OutlinedBuildingLibrary
            );

            $stats = $this->appendVisibleStat(
                $stats,
                'Comunidades visibles',
                Community::query()->visibleTo($user)->count(),
                Heroicon::OutlinedUsers
            );

            $stats = $this->appendVisibleStat(
                $stats,
                'Sacerdotes visibles',
                Priest::query()->visibleTo($user)->count(),
                Heroicon::OutlinedUserGroup
            );

            $stats = $this->appendVisibleStat(
                $stats,
                'Asignaciones vigentes',
                ParishPriestAssignment::query()->visibleTo($user)->where('is_current', true)->count(),
                Heroicon::OutlinedClipboardDocumentList
            );
        }

        if ($user->isTechnicalSupport()) {
            $stats = [
                ...$stats,
                Stat::make('Usuarios activos', $this->formatCount(User::query()->where('is_active', true)->count()))
                    ->icon(Heroicon::OutlinedUserCircle),
                Stat::make('Roles definidos', $this->formatCount(Role::query()->count()))
                    ->icon(Heroicon::OutlinedShieldCheck),
            ];
        }

        return $stats;
    }

    /**
     * @param  array<Stat>  $stats
     * @return array<Stat>
     */
    private function appendVisibleStat(array $stats, string $label, int $count, Heroicon $icon): array
    {
        if ($count <= 0) {
            return $stats;
        }

        return [
            ...$stats,
            Stat::make($label, $this->formatCount($count))->icon($icon),
        ];
    }

    private function formatCount(int $value): string
    {
        return number_format($value, 0, ',', '.');
    }

    private function formatCurrency(float $value): string
    {
        return '$ '.number_format($value, 0, ',', '.');
    }
}
