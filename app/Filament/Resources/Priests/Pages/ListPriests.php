<?php

namespace App\Filament\Resources\Priests\Pages;

use App\Filament\Resources\Priests\PriestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPriests extends ListRecords
{
    protected static string $resource = PriestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
