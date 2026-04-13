<?php

namespace App\Filament\Resources\ParishPriestAssignments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ParishPriestAssignmentsTable
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
                        TextColumn::make('priest.name')
                            ->label('Sacerdote')
                            ->placeholder('-'),
                        TextColumn::make('parish.name')
                            ->label('Parroquia')
                            ->placeholder('-'),
                        TextColumn::make('parishRole.description')
                            ->label('Cargo parroquial')
                            ->placeholder('-'),
                        IconColumn::make('is_current')
                            ->label('Vigente')
                            ->boolean(),
                    ])->space(1),
                ])->hiddenFrom('md'),
                TextColumn::make('parish.name')
                    ->label('Parroquia')
                    ->sortable()
                    ->searchable()
                    ->visibleFrom('md'),
                TextColumn::make('priest.name')
                    ->label('Sacerdote')
                    ->sortable()
                    ->searchable()
                    ->visibleFrom('md'),
                TextColumn::make('parishRole.description')
                    ->label('Cargo parroquial')
                    ->sortable()
                    ->visibleFrom('md'),
                IconColumn::make('is_current')
                    ->label('Vigente')
                    ->boolean()
                    ->visibleFrom('md'),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->visibleFrom('md'),
            ])
            ->filters([
                SelectFilter::make('parish')
                    ->label('Parroquia')
                    ->relationship('parish', 'name'),
                SelectFilter::make('priest')
                    ->label('Sacerdote')
                    ->relationship('priest', 'name'),
                SelectFilter::make('parishRole')
                    ->relationship('parishRole', 'description')
                    ->label('Cargo parroquial'),
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
