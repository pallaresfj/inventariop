<?php

namespace App\Filament\Resources\Parroquias\Pages;

use App\Filament\Resources\Parroquias\ParroquiaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListParroquias extends ListRecords
{
    protected static string $resource = ParroquiaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
