<?php

namespace App\Filament\Resources\AsignacionParroquiaSacerdotes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AsignacionParroquiaSacerdotesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('parroquia.nombre')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('sacerdote.nombre')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('cargoParroquial.descripcion')
                    ->label('Cargo')
                    ->sortable(),
                IconColumn::make('vigente')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('parroquia')
                    ->relationship('parroquia', 'nombre'),
                SelectFilter::make('sacerdote')
                    ->relationship('sacerdote', 'nombre'),
                SelectFilter::make('cargoParroquial')
                    ->relationship('cargoParroquial', 'descripcion')
                    ->label('Cargo'),
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
