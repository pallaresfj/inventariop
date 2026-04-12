<?php

namespace App\Filament\Resources\ParishPriestAssignments\Pages;

use App\Filament\Resources\ParishPriestAssignments\ParishPriestAssignmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListParishPriestAssignments extends ListRecords
{
    protected static string $resource = ParishPriestAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
