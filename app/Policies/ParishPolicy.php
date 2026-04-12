<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Parish;
use Illuminate\Auth\Access\HandlesAuthorization;

class ParishPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Parish');
    }

    public function view(AuthUser $authUser, Parish $parish): bool
    {
        return $authUser->can('View:Parish');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Parish');
    }

    public function update(AuthUser $authUser, Parish $parish): bool
    {
        return $authUser->can('Update:Parish');
    }

    public function delete(AuthUser $authUser, Parish $parish): bool
    {
        return $authUser->can('Delete:Parish');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Parish');
    }

    public function restore(AuthUser $authUser, Parish $parish): bool
    {
        return $authUser->can('Restore:Parish');
    }

    public function forceDelete(AuthUser $authUser, Parish $parish): bool
    {
        return $authUser->can('ForceDelete:Parish');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Parish');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Parish');
    }

    public function replicate(AuthUser $authUser, Parish $parish): bool
    {
        return $authUser->can('Replicate:Parish');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Parish');
    }

}