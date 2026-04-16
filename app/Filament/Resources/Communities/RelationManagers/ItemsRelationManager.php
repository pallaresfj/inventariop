<?php

namespace App\Filament\Resources\Communities\RelationManagers;

use App\Filament\Support\PublicImagePathResolver;
use App\Models\Community;
use App\Models\Item;
use Filament\Actions\Action;
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
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;

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
            ->defaultPaginationPageOption(10)
            ->searchDebounce('750ms')
            ->deferFilters()
            ->filtersFormColumns(['md' => 2])
            ->filtersLayout(FiltersLayout::Modal)
            ->persistFiltersInSession()
            ->recordActionsPosition(RecordActionsPosition::AfterContent)
            ->stackedOnMobile()
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Foto')
                    ->disk('public')
                    ->state(fn (Item $record): ?string => PublicImagePathResolver::resolveExistingState($record->image_path))
                    ->imageSize(44)
                    ->action(
                        Action::make('previewItemImageFromCommunity')
                            ->label('Ver imagen')
                            ->modalHeading('Imagen del articulo')
                            ->modalSubmitAction(false)
                            ->modalContent(fn (Item $record): View => view('filament.modals.image-preview', [
                                'imageUrl' => PublicImagePathResolver::resolveExistingUrl($record->image_path),
                                'alt' => $record->name,
                            ])),
                    )
                    ->disabledClick(fn (?string $state): bool => blank($state)),
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
