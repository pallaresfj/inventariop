<?php

namespace App\Filament\Resources\Priests\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
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
                    ->searchable()
                    ->sortable(),
                TextColumn::make('priestTitle.title')
                    ->label('Title')
                    ->sortable(),
                TextColumn::make('phone'),
                TextColumn::make('email')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('priest_title_id')
                    ->relationship('priestTitle', 'title')
                    ->label('Priest title'),
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
