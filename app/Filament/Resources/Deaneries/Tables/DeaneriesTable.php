<?php

namespace App\Filament\Resources\Deaneries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;

class DeaneriesTable
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
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('archpriest.name')
                    ->label('Arcipreste')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->visibleFrom('md'),
            ])
            ->filters([])
            ->recordActions([
                ReplicateAction::make(),
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
