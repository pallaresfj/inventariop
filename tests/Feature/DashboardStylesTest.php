<?php

test('dashboard styles include dark mode selectors for inventory stats', function (): void {
    $styles = file_get_contents(resource_path('views/filament/admin/dashboard-styles.blade.php'));

    expect($styles)
        ->toContain('.dark .fi-wi-stats-overview-stat.inv-stat')
        ->toContain('.dark .fi-wi-stats-overview-stat.inv-stat--hero')
        ->toContain('.dark .fi-wi-stats-overview-stat.inv-stat .fi-wi-stats-overview-stat-label')
        ->toContain('.dark .fi-wi-stats-overview-stat.inv-stat .fi-wi-stats-overview-stat-chart');
});
