<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ParishRole;
use Illuminate\Auth\Access\HandlesAuthorization;

class ParishRolePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ParishRole');
    }

    public function view(AuthUser $authUser, ParishRole $parishRole): bool
    {
        return $authUser->can('View:ParishRole');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ParishRole');
    }

    public function update(AuthUser $authUser, ParishRole $parishRole): bool
    {
        return $authUser->can('Update:ParishRole');
    }

    public function delete(AuthUser $authUser, ParishRole $parishRole): bool
    {
        return $authUser->can('Delete:ParishRole');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ParishRole');
    }

    public function restore(AuthUser $authUser, ParishRole $parishRole): bool
    {
        return $authUser->can('Restore:ParishRole');
    }

    public function forceDelete(AuthUser $authUser, ParishRole $parishRole): bool
    {
        return $authUser->can('ForceDelete:ParishRole');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ParishRole');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ParishRole');
    }

    public function replicate(AuthUser $authUser, ParishRole $parishRole): bool
    {
        return $authUser->can('Replicate:ParishRole');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ParishRole');
    }

}