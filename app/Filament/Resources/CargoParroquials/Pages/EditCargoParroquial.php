<?php

namespace App\Filament\Resources\CargoParroquials\Pages;

use App\Filament\Resources\CargoParroquials\CargoParroquialResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCargoParroquial extends EditRecord
{
    protected static string $resource = CargoParroquialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
