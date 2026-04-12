<?php

namespace App\Filament\Resources\AsignacionParroquiaSacerdotes\Pages;

use App\Filament\Resources\AsignacionParroquiaSacerdotes\AsignacionParroquiaSacerdoteResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAsignacionParroquiaSacerdote extends EditRecord
{
    protected static string $resource = AsignacionParroquiaSacerdoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
