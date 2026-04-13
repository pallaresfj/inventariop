<?php

namespace App\Filament\Widgets\Concerns;

use Carbon\CarbonImmutable;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

trait BuildsInventoryTrends
{
    /**
     * @return array{current_start: CarbonImmutable, current_end: CarbonImmutable, previous_start: CarbonImmutable, previous_end: CarbonImmutable}
     */
    protected function getMonthlyRanges(): array
    {
        $currentStart = CarbonImmutable::now()->startOfMonth();
        $currentEnd = $currentStart->endOfMonth();
        $previousStart = $currentStart->subMonthNoOverflow();
        $previousEnd = $currentStart->subSecond();

        return [
            'current_start' => $currentStart,
            'current_end' => $currentEnd,
            'previous_start' => $previousStart,
            'previous_end' => $previousEnd,
        ];
    }

    /**
     * @return array{
     *     current: int,
     *     previous: int,
     *     description: string,
     *     icon: Heroicon,
     *     color: string,
     *     chart: array<string, int|float>
     * }
     */
    protected function monthlyCountTrend(Builder $query, string $column): array
    {
        $ranges = $this->getMonthlyRanges();

        $previous = (clone $query)
            ->whereBetween($column, [$ranges['previous_start'], $ranges['previous_end']])
            ->count();

        $current = (clone $query)
            ->whereBetween($column, [$ranges['current_start'], $ranges['current_end']])
            ->count();

        [$description, $icon, $color] = $this->trendDescriptor($current, $previous, false);

        return [
            'current' => $current,
            'previous' => $previous,
            'description' => $description,
            'icon' => $icon,
            'color' => $color,
            'chart' => [
                'Mes anterior' => $previous,
                'Mes actual' => $current,
            ],
        ];
    }

    /**
     * @return array{
     *     current: float,
     *     previous: float,
     *     description: string,
     *     icon: Heroicon,
     *     color: string,
     *     chart: array<string, int|float>
     * }
     */
    protected function monthlySumTrend(Builder $query, string $dateColumn, string $sumColumn): array
    {
        $ranges = $this->getMonthlyRanges();

        $previous = (float) (clone $query)
            ->whereBetween($dateColumn, [$ranges['previous_start'], $ranges['previous_end']])
            ->sum($sumColumn);

        $current = (float) (clone $query)
            ->whereBetween($dateColumn, [$ranges['current_start'], $ranges['current_end']])
            ->sum($sumColumn);

        [$description, $icon, $color] = $this->trendDescriptor($current, $previous, true);

        return [
            'current' => $current,
            'previous' => $previous,
            'description' => $description,
            'icon' => $icon,
            'color' => $color,
            'chart' => [
                'Mes anterior' => $previous,
                'Mes actual' => $current,
            ],
        ];
    }

    protected function neutralDescriptor(string $description): array
    {
        return [
            'description' => $description,
            'icon' => Heroicon::OutlinedCalendarDays,
            'color' => 'info',
        ];
    }

    protected function formatCount(int $value): string
    {
        return number_format($value, 0, ',', '.');
    }

    protected function formatCurrency(float $value): string
    {
        return '$ '.number_format($value, 0, ',', '.');
    }

    protected function trendDescriptor(float $current, float $previous, bool $currency): array
    {
        $delta = $current - $previous;

        if ($delta > 0) {
            $formattedDelta = $currency
                ? $this->formatCurrency($delta)
                : $this->formatCount((int) $delta);

            return [
                "+{$formattedDelta} vs mes anterior",
                Heroicon::OutlinedArrowTrendingUp,
                'success',
            ];
        }

        if ($delta < 0) {
            $formattedDelta = $currency
                ? $this->formatCurrency(abs($delta))
                : $this->formatCount((int) abs($delta));

            return [
                "-{$formattedDelta} vs mes anterior",
                Heroicon::OutlinedArrowTrendingDown,
                'danger',
            ];
        }

        return [
            'Sin variacion vs mes anterior',
            Heroicon::OutlinedMinus,
            'info',
        ];
    }
}
