<?php

namespace App\Filament\Resources\Communities\Schemas;

use App\Filament\Resources\Communities\Support\CommunityScope;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class CommunityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Comunidad')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General')
                            ->schema([
                                Select::make('parish_id')
                                    ->label('Parroquia')
                                    ->relationship('parish', 'name')
                                    ->required()
                                    ->default(fn (): ?int => CommunityScope::resolveScopedParishId())
                                    ->disabled(fn (): bool => CommunityScope::shouldLockParish())
                                    ->dehydrated()
                                    ->searchable()
                                    ->preload(),
                                TextInput::make('name')
                                    ->label('Nombre')
                                    ->required()
                                    ->maxLength(80),
                            ])
                            ->columns(2),
                        Tab::make('Contacto')
                            ->schema([
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
                                TextInput::make('address')
                                    ->label('Direccion')
                                    ->maxLength(120)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                        Tab::make('Contenido')
                            ->schema([
                                RichEditor::make('description')
                                    ->label('Descripcion')
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),
                        Tab::make('Multimedia')
                            ->schema([
                                FileUpload::make('image_path')
                                    ->label('Imagen')
                                    ->disk('public')
                                    ->directory('inventory/communities')
                                    ->image()
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }
}
