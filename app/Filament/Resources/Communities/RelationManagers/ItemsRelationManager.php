<?php

namespace App\Filament\Resources\Communities\RelationManagers;

use App\Models\Community;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Articulos';

    protected static ?string $modelLabel = 'Articulo';

    protected static ?string $pluralModelLabel = 'Articulos';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(120)
                    ->columnSpanFull(),
                Select::make('condition')
                    ->label('Estado')
                    ->options([
                        'B' => 'Bueno',
                        'M' => 'Malo',
                        'R' => 'Restaurado',
                    ])
                    ->required(),
                Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true),
                TextInput::make('price')
                    ->label('Precio')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),
                DatePicker::make('acquired_at')
                    ->label('Fecha de adquisicion'),
                RichEditor::make('description')
                    ->label('Descripcion')
                    ->columnSpanFull(),
                FileUpload::make('image_path')
                    ->label('Imagen')
                    ->disk('public')
                    ->directory('inventory/items')
                    ->image()
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Precio')
                    ->money('COP', locale: 'es_CO')
                    ->sortable(),
                TextColumn::make('acquired_at')
                    ->label('Adquisicion')
                    ->date('Y-m-d')
                    ->sortable(),
                TextColumn::make('condition')
                    ->label('Estado')
                    ->badge(),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Agregar articulo')
                    ->mutateDataUsing(function (array $data): array {
                        /** @var Community $community */
                        $community = $this->getOwnerRecord();

                        return [
                            ...$data,
                            'community_id' => $community->getKey(),
                            'parish_id' => $community->parish_id,
                        ];
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                ReplicateAction::make(),
                EditAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        /** @var Community $community */
                        $community = $this->getOwnerRecord();

                        return [
                            ...$data,
                            'community_id' => $community->getKey(),
                            'parish_id' => $community->parish_id,
                        ];
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
