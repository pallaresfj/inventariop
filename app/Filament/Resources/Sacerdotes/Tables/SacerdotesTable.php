<?php

namespace App\Filament\Resources\Sacerdotes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SacerdotesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tituloSacerdotal.titulo')
                    ->label('Título')
                    ->sortable(),
                TextColumn::make('telefono'),
                TextColumn::make('correo')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('titulo_sacerdotal_id')
                    ->relationship('tituloSacerdotal', 'titulo')
                    ->label('Título sacerdotal'),
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
