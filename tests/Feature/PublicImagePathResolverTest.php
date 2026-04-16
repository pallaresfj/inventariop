<?php

use App\Filament\Support\PublicImagePathResolver;
use Illuminate\Support\Facades\Storage;

it('resolves existing storage paths from normalized inputs', function (): void {
    Storage::fake('public');
    Storage::disk('public')->put('inventory/items/sample.jpg', 'content');

    expect(PublicImagePathResolver::resolveExistingState('storage/inventory/items/sample.jpg'))
        ->toBe('inventory/items/sample.jpg')
        ->and(PublicImagePathResolver::resolveExistingState('/storage/app/public/inventory/items/sample.jpg'))
        ->toBe('inventory/items/sample.jpg')
        ->and(PublicImagePathResolver::resolveExistingState('https://app.example.com/storage/inventory/items/sample.jpg'))
        ->toBe('inventory/items/sample.jpg');
});

it('passes through external urls that are not served by local storage', function (): void {
    expect(PublicImagePathResolver::resolveExistingState('https://cdn.example.com/priests/photo.jpg'))
        ->toBe('https://cdn.example.com/priests/photo.jpg');
});

it('resolves legacy priest paths using path transforms', function (): void {
    Storage::fake('public');
    Storage::disk('public')->put('inventory-legacy/priests/7.jpg', 'content');

    $resolvedPath = PublicImagePathResolver::resolveExistingState('inventario-legacy/sacerdotes/7.jpg', [
        fn (string $path): string => str_replace('inventario-legacy/sacerdotes/', 'inventory-legacy/priests/', $path),
    ]);

    expect($resolvedPath)->toBe('inventory-legacy/priests/7.jpg');
});

it('returns null for missing local files', function (): void {
    Storage::fake('public');

    expect(PublicImagePathResolver::resolveExistingState('inventory/items/missing.jpg'))
        ->toBeNull();
});

it('builds a public url only when the file exists on the public disk', function (): void {
    Storage::fake('public');
    config(['filesystems.disks.public.url' => '/storage']);

    Storage::disk('public')->put('inventory/items/sample.jpg', 'content');

    expect(PublicImagePathResolver::resolveExistingUrl('inventory/items/sample.jpg'))
        ->toContain('/storage/inventory/items/sample.jpg')
        ->and(PublicImagePathResolver::resolveExistingUrl('inventory/items/missing.jpg'))
        ->toBeNull()
        ->and(PublicImagePathResolver::resolveExistingUrl('https://cdn.example.com/priests/photo.jpg'))
        ->toBe('https://cdn.example.com/priests/photo.jpg');
});
