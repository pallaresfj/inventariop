<?php

namespace App\Filament\Resources\Sacerdotes\Pages;

use App\Filament\Resources\Sacerdotes\SacerdoteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSacerdotes extends ListRecords
{
    protected static string $resource = SacerdoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
