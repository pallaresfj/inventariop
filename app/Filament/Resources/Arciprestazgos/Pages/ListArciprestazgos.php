<?php

namespace App\Filament\Resources\Arciprestazgos\Pages;

use App\Filament\Resources\Arciprestazgos\ArciprestazgoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListArciprestazgos extends ListRecords
{
    protected static string $resource = ArciprestazgoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
