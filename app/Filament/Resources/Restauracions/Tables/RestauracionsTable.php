<?php

namespace App\Filament\Resources\Restauracions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RestauracionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('articulo.nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fecha_restauracion')
                    ->date('Y-m-d')
                    ->sortable(),
                TextColumn::make('costo_restauracion')
                    ->money('COP', locale: 'es_CO')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('articulo')
                    ->relationship('articulo', 'nombre'),
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
