<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AsignacionParroquiaSacerdote;
use Illuminate\Auth\Access\HandlesAuthorization;

class AsignacionParroquiaSacerdotePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AsignacionParroquiaSacerdote');
    }

    public function view(AuthUser $authUser, AsignacionParroquiaSacerdote $asignacionParroquiaSacerdote): bool
    {
        return $authUser->can('View:AsignacionParroquiaSacerdote');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AsignacionParroquiaSacerdote');
    }

    public function update(AuthUser $authUser, AsignacionParroquiaSacerdote $asignacionParroquiaSacerdote): bool
    {
        return $authUser->can('Update:AsignacionParroquiaSacerdote');
    }

    public function delete(AuthUser $authUser, AsignacionParroquiaSacerdote $asignacionParroquiaSacerdote): bool
    {
        return $authUser->can('Delete:AsignacionParroquiaSacerdote');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AsignacionParroquiaSacerdote');
    }

    public function restore(AuthUser $authUser, AsignacionParroquiaSacerdote $asignacionParroquiaSacerdote): bool
    {
        return $authUser->can('Restore:AsignacionParroquiaSacerdote');
    }

    public function forceDelete(AuthUser $authUser, AsignacionParroquiaSacerdote $asignacionParroquiaSacerdote): bool
    {
        return $authUser->can('ForceDelete:AsignacionParroquiaSacerdote');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AsignacionParroquiaSacerdote');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AsignacionParroquiaSacerdote');
    }

    public function replicate(AuthUser $authUser, AsignacionParroquiaSacerdote $asignacionParroquiaSacerdote): bool
    {
        return $authUser->can('Replicate:AsignacionParroquiaSacerdote');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AsignacionParroquiaSacerdote');
    }

}