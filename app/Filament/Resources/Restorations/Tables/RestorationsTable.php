<?php

namespace App\Filament\Resources\Restorations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RestorationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('item.name')
                    ->label('Articulo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('restored_at')
                    ->label('Fecha de restauracion')
                    ->date('Y-m-d')
                    ->sortable(),
                TextColumn::make('restoration_cost')
                    ->label('Costo de restauracion')
                    ->money('COP', locale: 'es_CO')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('item')
                    ->label('Articulo')
                    ->relationship('item', 'name'),
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
