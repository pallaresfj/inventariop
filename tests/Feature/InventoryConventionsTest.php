<?php

use App\Filament\Resources\Communities\CommunityResource;
use App\Filament\Resources\Communities\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\Deaneries\DeaneryResource;
use App\Filament\Resources\Deaneries\RelationManagers\ParishesRelationManager;
use App\Filament\Resources\Parishes\ParishResource;
use App\Filament\Resources\Parishes\RelationManagers\CommunitiesRelationManager;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Route;

it('registers inventory artisan commands', function (): void {
    $commands = array_keys(app(Kernel::class)->all());

    expect($commands)->toContain(
        'inventory:import',
        'inventory:import-media',
        'inventory:reconcile',
        'inventory:bootstrap-admin',
        'inventory:force-password-reset',
    );
});

it('uses english admin resource slugs', function (): void {
    expect(Route::has('filament.admin.resources.deaneries.index'))->toBeTrue()
        ->and(Route::has('filament.admin.resources.parishes.index'))->toBeTrue()
        ->and(Route::has('filament.admin.resources.communities.index'))->toBeTrue()
        ->and(Route::has('filament.admin.resources.items.index'))->toBeTrue()
        ->and(Route::has('filament.admin.resources.restorations.index'))->toBeFalse()
        ->and(Route::has('filament.admin.resources.priests.index'))->toBeTrue();

    expect(route('filament.admin.resources.deaneries.index', absolute: false))->toBe('/admin/deaneries')
        ->and(route('filament.admin.resources.parishes.index', absolute: false))->toBe('/admin/parishes')
        ->and(route('filament.admin.resources.communities.index', absolute: false))->toBe('/admin/communities')
        ->and(route('filament.admin.resources.items.index', absolute: false))->toBe('/admin/items')
        ->and(route('filament.admin.resources.parish-priest-assignments.index', absolute: false))->toBe('/admin/parish-priest-assignments');
});

it('returns 404 for direct restorations url', function (): void {
    $this->get('/admin/restorations')->assertNotFound();
});

it('registers hierarchy relation managers for inventory resources', function (): void {
    expect(DeaneryResource::getRelations())->toContain(ParishesRelationManager::class)
        ->and(ParishResource::getRelations())->toContain(CommunitiesRelationManager::class)
        ->and(CommunityResource::getRelations())->toContain(ItemsRelationManager::class);
});
