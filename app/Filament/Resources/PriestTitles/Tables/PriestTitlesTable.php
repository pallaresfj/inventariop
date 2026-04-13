<?php

namespace App\Filament\Resources\PriestTitles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;

class PriestTitlesTable
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
                TextColumn::make('title')
                    ->label('Titulo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Descripcion')
                    ->searchable()
                    ->lineClamp(2),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([])
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
}
