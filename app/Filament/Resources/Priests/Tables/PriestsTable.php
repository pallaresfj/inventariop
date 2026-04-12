<?php

namespace App\Filament\Resources\Priests\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PriestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('priestTitle.title')
                    ->label('Titulo')
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('Telefono'),
                TextColumn::make('email')
                    ->label('Correo')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('priest_title_id')
                    ->relationship('priestTitle', 'title')
                    ->label('Titulo sacerdotal'),
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
