<?php

namespace App\Filament\Resources\Articulos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ArticulosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('parroquia.nombre')
                    ->sortable(),
                TextColumn::make('comunidad.nombre')
                    ->sortable(),
                TextColumn::make('estado')
                    ->badge(),
                TextColumn::make('precio')
                    ->money('COP', locale: 'es_CO')
                    ->sortable(),
                TextColumn::make('fecha_adquisicion')
                    ->date('Y-m-d')
                    ->sortable(),
                IconColumn::make('activo')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('parroquia')
                    ->relationship('parroquia', 'nombre'),
                SelectFilter::make('comunidad')
                    ->relationship('comunidad', 'nombre'),
                SelectFilter::make('estado')
                    ->options([
                        'B' => 'Bueno',
                        'M' => 'Malo',
                        'R' => 'Restaurado',
                    ]),
                TernaryFilter::make('activo'),
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
