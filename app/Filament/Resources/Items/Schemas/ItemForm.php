<?php

namespace App\Filament\Resources\Items\Schemas;

use App\Filament\Resources\Items\Support\ItemScope;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class ItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Articulo')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre')
                                    ->required()
                                    ->maxLength(120)
                                    ->columnSpanFull(),
                                Select::make('parish_id')
                                    ->label('Parroquia')
                                    ->relationship('parish', 'name')
                                    ->required()
                                    ->live()
                                    ->default(fn (): ?int => ItemScope::resolveScopedParishId())
                                    ->disabled(fn (): bool => ItemScope::shouldLockParish())
                                    ->dehydrated()
                                    ->afterStateUpdated(function (Set $set, mixed $state, mixed $old): void {
                                        if (ItemScope::shouldResetCommunity($state, $old)) {
                                            $set('community_id', null);
                                        }
                                    })
                                    ->searchable()
                                    ->preload(),
                                Select::make('community_id')
                                    ->label('Comunidad')
                                    ->relationship(
                                        'community',
                                        'name',
                                        fn (Builder $query, Get $get): Builder => ItemScope::scopeCommunityOptionsQuery(
                                            $query,
                                            ItemScope::resolveSelectedParishId($get('parish_id'))
                                        ),
                                    )
                                    ->required()
                                    ->default(fn (): ?int => ItemScope::resolveScopedCommunityId())
                                    ->disabled(fn (): bool => ItemScope::shouldLockCommunity())
                                    ->dehydrated()
                                    ->searchable()
                                    ->preload(),
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
                            ])
                            ->columns(2),
                        Tab::make('Detalles')
                            ->schema([
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
                            ])
                            ->columns(2),
                        Tab::make('Multimedia')
                            ->schema([
                                FileUpload::make('image_path')
                                    ->label('Imagen')
                                    ->disk('public')
                                    ->directory('inventory/items')
                                    ->image()
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }
}
