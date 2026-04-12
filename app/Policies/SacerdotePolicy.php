<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Sacerdote;
use Illuminate\Auth\Access\HandlesAuthorization;

class SacerdotePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Sacerdote');
    }

    public function view(AuthUser $authUser, Sacerdote $sacerdote): bool
    {
        return $authUser->can('View:Sacerdote');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Sacerdote');
    }

    public function update(AuthUser $authUser, Sacerdote $sacerdote): bool
    {
        return $authUser->can('Update:Sacerdote');
    }

    public function delete(AuthUser $authUser, Sacerdote $sacerdote): bool
    {
        return $authUser->can('Delete:Sacerdote');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Sacerdote');
    }

    public function restore(AuthUser $authUser, Sacerdote $sacerdote): bool
    {
        return $authUser->can('Restore:Sacerdote');
    }

    public function forceDelete(AuthUser $authUser, Sacerdote $sacerdote): bool
    {
        return $authUser->can('ForceDelete:Sacerdote');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Sacerdote');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Sacerdote');
    }

    public function replicate(AuthUser $authUser, Sacerdote $sacerdote): bool
    {
        return $authUser->can('Replicate:Sacerdote');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Sacerdote');
    }

}