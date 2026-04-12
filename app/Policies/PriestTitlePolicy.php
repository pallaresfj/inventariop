<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PriestTitle;
use Illuminate\Auth\Access\HandlesAuthorization;

class PriestTitlePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PriestTitle');
    }

    public function view(AuthUser $authUser, PriestTitle $priestTitle): bool
    {
        return $authUser->can('View:PriestTitle');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PriestTitle');
    }

    public function update(AuthUser $authUser, PriestTitle $priestTitle): bool
    {
        return $authUser->can('Update:PriestTitle');
    }

    public function delete(AuthUser $authUser, PriestTitle $priestTitle): bool
    {
        return $authUser->can('Delete:PriestTitle');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PriestTitle');
    }

    public function restore(AuthUser $authUser, PriestTitle $priestTitle): bool
    {
        return $authUser->can('Restore:PriestTitle');
    }

    public function forceDelete(AuthUser $authUser, PriestTitle $priestTitle): bool
    {
        return $authUser->can('ForceDelete:PriestTitle');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PriestTitle');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PriestTitle');
    }

    public function replicate(AuthUser $authUser, PriestTitle $priestTitle): bool
    {
        return $authUser->can('Replicate:PriestTitle');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PriestTitle');
    }

}