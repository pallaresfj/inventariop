<?php

namespace App\Filament\Resources\Parroquias\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ParroquiasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('arciprestazgo.nombre')
                    ->label('Arciprestazgo')
                    ->sortable(),
                TextColumn::make('legacy_login')
                    ->label('Login legado')
                    ->searchable(),
                TextColumn::make('correo')
                    ->searchable(),
                TextColumn::make('telefono'),
            ])
            ->filters([
                SelectFilter::make('arciprestazgo')
                    ->relationship('arciprestazgo', 'nombre'),
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
