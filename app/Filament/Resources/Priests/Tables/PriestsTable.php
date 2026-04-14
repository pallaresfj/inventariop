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
                    ->state(fn (Priest $record): ?string => self::normalizeImagePath($record->image_path))
                    ->checkFileExistence(false)
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

    private static function normalizeImagePath(?string $imagePath): ?string
    {
        if (blank($imagePath)) {
            return null;
        }

        $normalizedPath = str_replace('\\', '/', trim($imagePath));

        if (filter_var($normalizedPath, FILTER_VALIDATE_URL)) {
            return $normalizedPath;
        }

        foreach (['storage/app/public/', 'public/storage/', 'storage/'] as $prefix) {
            if (str_starts_with($normalizedPath, $prefix)) {
                $normalizedPath = substr($normalizedPath, strlen($prefix));
                break;
            }
        }

        return ltrim($normalizedPath, '/');
    }
}
