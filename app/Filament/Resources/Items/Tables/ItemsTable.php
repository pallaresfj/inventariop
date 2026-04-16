<?php

namespace App\Filament\Resources\Items\Tables;

use App\Filament\Support\PublicImagePathResolver;
use App\Models\Item;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;

class ItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
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
                        Action::make('previewItemImage')
                            ->label('Ver imagen')
                            ->modalHeading('Imagen del articulo')
                            ->modalSubmitAction(false)
                            ->modalContent(fn (Item $record): View => view('filament.modals.image-preview', [
                                'imageUrl' => PublicImagePathResolver::resolveExistingUrl($record->image_path),
                                'alt' => $record->name,
                            ])),
                    )
                    ->disabledClick(fn (?string $state): bool => blank($state)),
                TextColumn::make('parish.name')
                    ->label('Parroquia')
                    ->sortable(),
                TextColumn::make('community.name')
                    ->label('Comunidad')
                    ->sortable(),
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
                    ->sortable()
                    ->visibleFrom('md'),
                TextColumn::make('condition')
                    ->label('Estado')
                    ->badge()
                    ->visibleFrom('md'),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('parish')
                    ->label('Parroquia')
                    ->relationship('parish', 'name'),
                SelectFilter::make('community')
                    ->label('Comunidad')
                    ->relationship('community', 'name'),
                SelectFilter::make('condition')
                    ->label('Estado')
                    ->options([
                        'B' => 'Bueno',
                        'M' => 'Malo',
                        'R' => 'Restaurado',
                    ]),
                TernaryFilter::make('is_active')
                    ->label('Activo'),
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
