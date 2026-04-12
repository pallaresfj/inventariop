<?php

namespace App\Filament\Resources\ParishPriestAssignments\Pages;

use App\Filament\Resources\ParishPriestAssignments\ParishPriestAssignmentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditParishPriestAssignment extends EditRecord
{
    protected static string $resource = ParishPriestAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
