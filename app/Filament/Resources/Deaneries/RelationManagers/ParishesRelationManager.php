<?php

namespace App\Filament\Resources\Deaneries\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;

class ParishesRelationManager extends RelationManager
{
    protected static string $relationship = 'parishes';

    protected static ?string $title = 'Parroquias';

    protected static ?string $modelLabel = 'Parroquia';

    protected static ?string $pluralModelLabel = 'Parroquias';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(80),
                TextInput::make('legacy_login')
                    ->label('Acceso legado')
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Correo')
                    ->email()
                    ->maxLength(120),
                TextInput::make('phone')
                    ->label('Telefono')
                    ->maxLength(30),
                TextInput::make('web')
                    ->label('Sitio web')
                    ->maxLength(120),
                TextInput::make('address')
                    ->label('Direccion')
                    ->maxLength(120)
                    ->columnSpanFull(),
                RichEditor::make('description')
                    ->label('Descripcion')
                    ->columnSpanFull(),
                FileUpload::make('image_path')
                    ->label('Imagen')
                    ->disk('public')
                    ->directory('inventory/parishes')
                    ->image()
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultPaginationPageOption(10)
            ->searchDebounce('750ms')
            ->deferFilters()
            ->filtersFormColumns(['md' => 2])
            ->filtersLayout(FiltersLayout::Modal)
            ->persistFiltersInSession()
            ->recordActionsPosition(RecordActionsPosition::AfterContent)
            ->columns([
                Panel::make([
                    Stack::make([
                        TextColumn::make('name')
                            ->label('Nombre'),
                        TextColumn::make('legacy_login')
                            ->label('Acceso legado')
                            ->placeholder('-')
                            ->limit(24),
                        TextColumn::make('phone')
                            ->label('Telefono')
                            ->placeholder('-'),
                        TextColumn::make('email')
                            ->label('Correo')
                            ->placeholder('-')
                            ->limit(30),
                    ])->space(1),
                ])->hiddenFrom('md'),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('legacy_login')
                    ->label('Acceso legado')
                    ->searchable()
                    ->visibleFrom('md'),
                TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->visibleFrom('md'),
                TextColumn::make('phone')
                    ->label('Telefono')
                    ->visibleFrom('md'),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->visibleFrom('md'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Agregar parroquia'),
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
