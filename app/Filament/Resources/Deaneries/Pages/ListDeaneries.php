<?php

namespace App\Filament\Resources\Deaneries\Pages;

use App\Filament\Resources\Deaneries\DeaneryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDeaneries extends ListRecords
{
    protected static string $resource = DeaneryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
