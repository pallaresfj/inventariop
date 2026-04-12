<?php

namespace App\Filament\Resources\ParishPriestAssignments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ParishPriestAssignmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('parish.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('priest.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('parishRole.description')
                    ->label('Parish Role')
                    ->sortable(),
                IconColumn::make('is_current')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('parish')
                    ->relationship('parish', 'name'),
                SelectFilter::make('priest')
                    ->relationship('priest', 'name'),
                SelectFilter::make('parishRole')
                    ->relationship('parishRole', 'description')
                    ->label('Parish Role'),
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
