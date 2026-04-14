<?php

namespace App\Filament\Resources\Parishes\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ParishForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Parroquia')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General')
                            ->schema([
                                Select::make('deanery_id')
                                    ->label('Arciprestazgo')
                                    ->relationship('deanery', 'name')
                                    ->required()
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
                                    ->directory('inventory/parishes')
                                    ->image()
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }
}
