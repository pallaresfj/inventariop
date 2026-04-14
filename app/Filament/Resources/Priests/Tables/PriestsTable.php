<?php

namespace App\Filament\Resources\Priests\Tables;

use App\Models\Priest;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PriestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(10)
            ->searchDebounce('750ms')
            ->deferFilters()
            ->filtersFormColumns(['md' => 2])
            ->filtersLayout(FiltersLayout::Modal)
            ->persistFiltersInSession()
            ->recordActionsPosition(RecordActionsPosition::AfterContent)
            ->stackedOnMobile()
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Foto')
                    ->disk('public')
                    ->state(fn (Priest $record): ?string => self::resolveImageUrl($record->image_path))
                    ->imageSize(40)
                    ->circular(),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('priestTitle.title')
                    ->label('Titulo')
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('Telefono'),
                TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->limit(40),
            ])
            ->filters([
                SelectFilter::make('priest_title_id')
                    ->relationship('priestTitle', 'title')
                    ->label('Titulo sacerdotal'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function resolveImageUrl(?string $imagePath): ?string
    {
        if (blank($imagePath)) {
            return null;
        }

        $normalizedPath = str_replace('\\', '/', trim($imagePath));
        $normalizedPath = preg_replace('/\0+$/', '', $normalizedPath) ?: $normalizedPath;

        if (filter_var($normalizedPath, FILTER_VALIDATE_URL)) {
            $urlPath = parse_url($normalizedPath, PHP_URL_PATH);

            if (is_string($urlPath) && str_contains($urlPath, '/storage/')) {
                $normalizedPath = Str::after($urlPath, '/storage/');
            } else {
                return $normalizedPath;
            }
        }

        if (str_contains($normalizedPath, '/storage/app/public/')) {
            $normalizedPath = Str::after($normalizedPath, '/storage/app/public/');
        }

        foreach (['storage/app/public/', 'public/storage/', 'storage/'] as $prefix) {
            if (str_starts_with($normalizedPath, $prefix)) {
                $normalizedPath = substr($normalizedPath, strlen($prefix));
                break;
            }
        }

        $normalizedPath = ltrim($normalizedPath, '/');

        $candidatePaths = array_values(array_unique([
            $normalizedPath,
            str_replace('inventario-legacy/sacerdotes/', 'inventory-legacy/priests/', $normalizedPath),
            str_replace('inventory-legacy/priests/', 'inventario-legacy/sacerdotes/', $normalizedPath),
        ]));

        foreach ($candidatePaths as $candidatePath) {
            if ($candidatePath !== '' && Storage::disk('public')->exists($candidatePath)) {
                return Storage::disk('public')->url($candidatePath);
            }
        }

        return null;
    }
}
