<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Parroquia;
use Illuminate\Auth\Access\HandlesAuthorization;

class ParroquiaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Parroquia');
    }

    public function view(AuthUser $authUser, Parroquia $parroquia): bool
    {
        return $authUser->can('View:Parroquia');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Parroquia');
    }

    public function update(AuthUser $authUser, Parroquia $parroquia): bool
    {
        return $authUser->can('Update:Parroquia');
    }

    public function delete(AuthUser $authUser, Parroquia $parroquia): bool
    {
        return $authUser->can('Delete:Parroquia');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Parroquia');
    }

    public function restore(AuthUser $authUser, Parroquia $parroquia): bool
    {
        return $authUser->can('Restore:Parroquia');
    }

    public function forceDelete(AuthUser $authUser, Parroquia $parroquia): bool
    {
        return $authUser->can('ForceDelete:Parroquia');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Parroquia');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Parroquia');
    }

    public function replicate(AuthUser $authUser, Parroquia $parroquia): bool
    {
        return $authUser->can('Replicate:Parroquia');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Parroquia');
    }

}