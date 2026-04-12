<?php

namespace App\Filament\Resources\ParishPriestAssignments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
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
                    ->label('Parroquia')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('priest.name')
                    ->label('Sacerdote')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('parishRole.description')
                    ->label('Cargo parroquial')
                    ->sortable(),
                IconColumn::make('is_current')
                    ->label('Vigente')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('parish')
                    ->label('Parroquia')
                    ->relationship('parish', 'name'),
                SelectFilter::make('priest')
                    ->label('Sacerdote')
                    ->relationship('priest', 'name'),
                SelectFilter::make('parishRole')
                    ->relationship('parishRole', 'description')
                    ->label('Cargo parroquial'),
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
