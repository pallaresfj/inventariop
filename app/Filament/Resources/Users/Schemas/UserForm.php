<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Filament\Resources\Users\Support\UserLocationScope;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('username')
                    ->label('Usuario')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Correo')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('password')
                    ->label('Contrasena')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->maxLength(255),
                Select::make('roles')
                    ->label('Roles')
                    ->relationship('roles', 'name')
                    ->getOptionLabelFromRecordUsing(fn (Role $record): string => self::translateRole($record->name))
                    ->multiple()
                    ->preload()
                    ->searchable(),
                Select::make('deanery_id')
                    ->label('Arciprestazgo')
                    ->relationship('deanery', 'name')
                    ->live()
                    ->afterStateUpdated(function (Set $set, mixed $state, mixed $old): void {
                        if (! UserLocationScope::shouldResetParishAndCommunity($state, $old)) {
                            return;
                        }

                        $set('parish_id', null);
                        $set('community_id', null);
                    })
                    ->searchable()
                    ->preload(),
                Select::make('parish_id')
                    ->label('Parroquia')
                    ->relationship(
                        'parish',
                        'name',
                        fn (Builder $query, Get $get): Builder => UserLocationScope::scopeParishOptionsQuery(
                            $query,
                            UserLocationScope::resolveSelectedDeaneryId($get('deanery_id'))
                        ),
                    )
                    ->live()
                    ->afterStateUpdated(function (Set $set, mixed $state, mixed $old): void {
                        if (UserLocationScope::shouldResetCommunity($state, $old)) {
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
                        fn (Builder $query, Get $get): Builder => UserLocationScope::scopeCommunityOptionsQuery(
                            $query,
                            UserLocationScope::resolveSelectedParishId($get('parish_id'))
                        ),
                    )
                    ->searchable()
                    ->preload(),
                Toggle::make('is_active')
                    ->label('Usuario activo')
                    ->default(true),
                Toggle::make('force_password_reset')
                    ->label('Forzar cambio de contrasena')
                    ->default(false),
                FileUpload::make('picture_path')
                    ->label('Foto')
                    ->disk('public')
                    ->directory('inventory/users')
                    ->image(),
            ]);
    }

    private static function translateRole(string $roleName): string
    {
        return match ($roleName) {
            'technical_support' => 'Soporte tecnico',
            'diocese_manager' => 'Gestor diocesano',
            'parish_manager' => 'Gestor parroquial',
            'community_manager' => 'Gestor comunitario',
            default => $roleName,
        };
    }
}
