<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Priest;
use Illuminate\Auth\Access\HandlesAuthorization;

class PriestPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Priest');
    }

    public function view(AuthUser $authUser, Priest $priest): bool
    {
        return $authUser->can('View:Priest');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Priest');
    }

    public function update(AuthUser $authUser, Priest $priest): bool
    {
        return $authUser->can('Update:Priest');
    }

    public function delete(AuthUser $authUser, Priest $priest): bool
    {
        return $authUser->can('Delete:Priest');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Priest');
    }

    public function restore(AuthUser $authUser, Priest $priest): bool
    {
        return $authUser->can('Restore:Priest');
    }

    public function forceDelete(AuthUser $authUser, Priest $priest): bool
    {
        return $authUser->can('ForceDelete:Priest');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Priest');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Priest');
    }

    public function replicate(AuthUser $authUser, Priest $priest): bool
    {
        return $authUser->can('Replicate:Priest');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Priest');
    }

}