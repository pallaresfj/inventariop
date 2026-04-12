<?php

namespace App\Filament\Resources\PriestTitles\Pages;

use App\Filament\Resources\PriestTitles\PriestTitleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPriestTitles extends ListRecords
{
    protected static string $resource = PriestTitleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
