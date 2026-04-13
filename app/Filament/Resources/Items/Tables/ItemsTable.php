<?php

namespace App\Filament\Resources\Items\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ItemsTable
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
            ->columns([
                Panel::make([
                    Stack::make([
                        TextColumn::make('name')
                            ->label('Nombre'),
                        TextColumn::make('parish.name')
                            ->label('Parroquia')
                            ->placeholder('-'),
                        TextColumn::make('community.name')
                            ->label('Comunidad')
                            ->placeholder('-'),
                        TextColumn::make('price')
                            ->label('Precio')
                            ->money('COP', locale: 'es_CO'),
                        TextColumn::make('acquired_at')
                            ->label('Adquisicion')
                            ->date('Y-m-d')
                            ->placeholder('-'),
                        TextColumn::make('condition')
                            ->label('Estado')
                            ->badge(),
                        IconColumn::make('is_active')
                            ->label('Activo')
                            ->boolean(),
                    ])->space(1),
                ])->hiddenFrom('md'),
                TextColumn::make('parish.name')
                    ->label('Parroquia')
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('community.name')
                    ->label('Comunidad')
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('price')
                    ->label('Precio')
                    ->money('COP', locale: 'es_CO')
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('acquired_at')
                    ->label('Adquisicion')
                    ->date('Y-m-d')
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('condition')
                    ->label('Estado')
                    ->badge()
                    ->visibleFrom('md'),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->visibleFrom('md'),
            ])
            ->filters([
                SelectFilter::make('parish')
                    ->label('Parroquia')
                    ->relationship('parish', 'name'),
                SelectFilter::make('community')
                    ->label('Comunidad')
                    ->relationship('community', 'name'),
                SelectFilter::make('condition')
                    ->label('Estado')
                    ->options([
                        'B' => 'Bueno',
                        'M' => 'Malo',
                        'R' => 'Restaurado',
                    ]),
                TernaryFilter::make('is_active')
                    ->label('Activo'),
            ])
            ->recordActions([
                ViewAction::make(),
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
