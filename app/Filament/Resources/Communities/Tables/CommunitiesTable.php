<?php

namespace App\Filament\Resources\Communities\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CommunitiesTable
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
                TextColumn::make('parish.name')
                    ->label('Parroquia')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('legacy_login')
                    ->label('Acceso legado')
                    ->searchable()
                    ->limit(30)
                    ->visibleFrom('md'),
                TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('phone')
                    ->label('Telefono'),
            ])
            ->filters([
                SelectFilter::make('parish')
                    ->label('Parroquia')
                    ->relationship('parish', 'name'),
            ])
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
