<?php

namespace App\Filament\Resources\Parishes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ParishesTable
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
                        TextColumn::make('deanery.name')
                            ->label('Arciprestazgo')
                            ->placeholder('-'),
                        TextColumn::make('phone')
                            ->label('Telefono')
                            ->placeholder('-'),
                        TextColumn::make('email')
                            ->label('Correo')
                            ->placeholder('-')
                            ->limit(30),
                        TextColumn::make('legacy_login')
                            ->label('Acceso legado')
                            ->placeholder('-')
                            ->limit(24),
                    ])->space(1),
                ])->hiddenFrom('md'),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('deanery.name')
                    ->label('Arciprestazgo')
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('legacy_login')
                    ->label('Acceso legado')
                    ->searchable()
                    ->limit(30)
                    ->visibleFrom('md'),
                TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->limit(40)
                    ->visibleFrom('md'),
                TextColumn::make('phone')
                    ->label('Telefono')
                    ->visibleFrom('md'),
            ])
            ->filters([
                SelectFilter::make('deanery')
                    ->label('Arciprestazgo')
                    ->relationship('deanery', 'name'),
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
