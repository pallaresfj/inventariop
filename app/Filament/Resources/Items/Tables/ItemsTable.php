<?php

namespace App\Filament\Resources\Items\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('parish.name')
                    ->sortable(),
                TextColumn::make('community.name')
                    ->sortable(),
                TextColumn::make('condition')
                    ->badge(),
                TextColumn::make('price')
                    ->money('COP', locale: 'es_CO')
                    ->sortable(),
                TextColumn::make('acquired_at')
                    ->date('Y-m-d')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('parish')
                    ->relationship('parish', 'name'),
                SelectFilter::make('community')
                    ->relationship('community', 'name'),
                SelectFilter::make('condition')
                    ->options([
                        'B' => 'Good',
                        'M' => 'Poor',
                        'R' => 'Restored',
                    ]),
                TernaryFilter::make('is_active'),
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
