<?php

namespace App\Filament\Resources\Items\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;

class RestorationsRelationManager extends RelationManager
{
    protected static string $relationship = 'restorations';

    protected static ?string $title = 'Restauraciones';

    protected static ?string $modelLabel = 'Restauracion';

    protected static ?string $pluralModelLabel = 'Restauraciones';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('restored_at')
                    ->label('Fecha de restauracion')
                    ->required(),
                TextInput::make('restoration_cost')
                    ->label('Costo de restauracion')
                    ->numeric()
                    ->minValue(0),
                FileUpload::make('image_path')
                    ->label('Imagen')
                    ->disk('public')
                    ->directory('inventory/restorations')
                    ->image()
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('restored_at')
            ->defaultPaginationPageOption(10)
            ->searchDebounce('750ms')
            ->deferFilters()
            ->filtersFormColumns(['md' => 2])
            ->filtersLayout(FiltersLayout::Modal)
            ->persistFiltersInSession()
            ->recordActionsPosition(RecordActionsPosition::AfterContent)
            ->stackedOnMobile()
            ->columns([
                TextColumn::make('restored_at')
                    ->label('Fecha de restauracion')
                    ->date('Y-m-d')
                    ->sortable(),
                TextColumn::make('restoration_cost')
                    ->label('Costo de restauracion')
                    ->money('COP', locale: 'es_CO')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->visibleFrom('md'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Agregar restauracion'),
            ])
            ->recordActions([
                ViewAction::make(),
                ReplicateAction::make(),
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
