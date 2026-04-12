<?php

namespace App\Filament\Resources\TituloSacerdotals\Pages;

use App\Filament\Resources\TituloSacerdotals\TituloSacerdotalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTituloSacerdotals extends ListRecords
{
    protected static string $resource = TituloSacerdotalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
