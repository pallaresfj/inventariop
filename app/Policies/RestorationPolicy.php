<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Restoration;
use Illuminate\Auth\Access\HandlesAuthorization;

class RestorationPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Restoration');
    }

    public function view(AuthUser $authUser, Restoration $restoration): bool
    {
        return $authUser->can('View:Restoration');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Restoration');
    }

    public function update(AuthUser $authUser, Restoration $restoration): bool
    {
        return $authUser->can('Update:Restoration');
    }

    public function delete(AuthUser $authUser, Restoration $restoration): bool
    {
        return $authUser->can('Delete:Restoration');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Restoration');
    }

    public function restore(AuthUser $authUser, Restoration $restoration): bool
    {
        return $authUser->can('Restore:Restoration');
    }

    public function forceDelete(AuthUser $authUser, Restoration $restoration): bool
    {
        return $authUser->can('ForceDelete:Restoration');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Restoration');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Restoration');
    }

    public function replicate(AuthUser $authUser, Restoration $restoration): bool
    {
        return $authUser->can('Replicate:Restoration');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Restoration');
    }

}