<?php

namespace App\Filament\Resources\Restorations\Pages;

use App\Filament\Resources\Restorations\RestorationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRestorations extends ListRecords
{
    protected static string $resource = RestorationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
