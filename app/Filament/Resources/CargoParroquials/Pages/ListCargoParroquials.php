<?php

namespace App\Filament\Resources\CargoParroquials\Pages;

use App\Filament\Resources\CargoParroquials\CargoParroquialResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCargoParroquials extends ListRecords
{
    protected static string $resource = CargoParroquialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
