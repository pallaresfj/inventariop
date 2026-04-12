<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Comunidad;
use Illuminate\Auth\Access\HandlesAuthorization;

class ComunidadPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Comunidad');
    }

    public function view(AuthUser $authUser, Comunidad $comunidad): bool
    {
        return $authUser->can('View:Comunidad');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Comunidad');
    }

    public function update(AuthUser $authUser, Comunidad $comunidad): bool
    {
        return $authUser->can('Update:Comunidad');
    }

    public function delete(AuthUser $authUser, Comunidad $comunidad): bool
    {
        return $authUser->can('Delete:Comunidad');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Comunidad');
    }

    public function restore(AuthUser $authUser, Comunidad $comunidad): bool
    {
        return $authUser->can('Restore:Comunidad');
    }

    public function forceDelete(AuthUser $authUser, Comunidad $comunidad): bool
    {
        return $authUser->can('ForceDelete:Comunidad');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Comunidad');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Comunidad');
    }

    public function replicate(AuthUser $authUser, Comunidad $comunidad): bool
    {
        return $authUser->can('Replicate:Comunidad');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Comunidad');
    }

}