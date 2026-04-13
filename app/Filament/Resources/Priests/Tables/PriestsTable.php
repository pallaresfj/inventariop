<?php

namespace App\Filament\Resources\Priests\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Stack;
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
            ->columns([
                Panel::make([
                    Stack::make([
                        TextColumn::make('name')
                            ->label('Nombre'),
                        TextColumn::make('priestTitle.title')
                            ->label('Titulo')
                            ->placeholder('-'),
                        TextColumn::make('phone')
                            ->label('Telefono')
                            ->placeholder('-'),
                        TextColumn::make('email')
                            ->label('Correo')
                            ->placeholder('-')
                            ->limit(32),
                    ])->space(1),
                ])->hiddenFrom('md'),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('priestTitle.title')
                    ->label('Titulo')
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('phone')
                    ->label('Telefono')
                    ->visibleFrom('md'),
                TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->limit(40)
                    ->visibleFrom('md'),
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
}
