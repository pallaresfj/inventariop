<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ParishPriestAssignment;
use Illuminate\Auth\Access\HandlesAuthorization;

class ParishPriestAssignmentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ParishPriestAssignment');
    }

    public function view(AuthUser $authUser, ParishPriestAssignment $parishPriestAssignment): bool
    {
        return $authUser->can('View:ParishPriestAssignment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ParishPriestAssignment');
    }

    public function update(AuthUser $authUser, ParishPriestAssignment $parishPriestAssignment): bool
    {
        return $authUser->can('Update:ParishPriestAssignment');
    }

    public function delete(AuthUser $authUser, ParishPriestAssignment $parishPriestAssignment): bool
    {
        return $authUser->can('Delete:ParishPriestAssignment');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ParishPriestAssignment');
    }

    public function restore(AuthUser $authUser, ParishPriestAssignment $parishPriestAssignment): bool
    {
        return $authUser->can('Restore:ParishPriestAssignment');
    }

    public function forceDelete(AuthUser $authUser, ParishPriestAssignment $parishPriestAssignment): bool
    {
        return $authUser->can('ForceDelete:ParishPriestAssignment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ParishPriestAssignment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ParishPriestAssignment');
    }

    public function replicate(AuthUser $authUser, ParishPriestAssignment $parishPriestAssignment): bool
    {
        return $authUser->can('Replicate:ParishPriestAssignment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ParishPriestAssignment');
    }

}