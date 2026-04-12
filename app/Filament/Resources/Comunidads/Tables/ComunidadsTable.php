<?php

namespace App\Filament\Resources\Comunidads\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ComunidadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('parroquia.nombre')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('legacy_login')
                    ->label('Login legado')
                    ->searchable(),
                TextColumn::make('correo')
                    ->searchable(),
                TextColumn::make('telefono'),
            ])
            ->filters([
                SelectFilter::make('parroquia')
                    ->relationship('parroquia', 'nombre'),
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
