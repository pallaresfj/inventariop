<?php

namespace App\Filament\Resources\AsignacionParroquiaSacerdotes\Pages;

use App\Filament\Resources\AsignacionParroquiaSacerdotes\AsignacionParroquiaSacerdoteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAsignacionParroquiaSacerdotes extends ListRecords
{
    protected static string $resource = AsignacionParroquiaSacerdoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
