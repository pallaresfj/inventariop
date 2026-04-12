<?php

namespace App\Filament\Resources\Restauracions\Pages;

use App\Filament\Resources\Restauracions\RestauracionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRestauracions extends ListRecords
{
    protected static string $resource = RestauracionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
