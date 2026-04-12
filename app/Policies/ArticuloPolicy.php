<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Articulo;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArticuloPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Articulo');
    }

    public function view(AuthUser $authUser, Articulo $articulo): bool
    {
        return $authUser->can('View:Articulo');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Articulo');
    }

    public function update(AuthUser $authUser, Articulo $articulo): bool
    {
        return $authUser->can('Update:Articulo');
    }

    public function delete(AuthUser $authUser, Articulo $articulo): bool
    {
        return $authUser->can('Delete:Articulo');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Articulo');
    }

    public function restore(AuthUser $authUser, Articulo $articulo): bool
    {
        return $authUser->can('Restore:Articulo');
    }

    public function forceDelete(AuthUser $authUser, Articulo $articulo): bool
    {
        return $authUser->can('ForceDelete:Articulo');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Articulo');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Articulo');
    }

    public function replicate(AuthUser $authUser, Articulo $articulo): bool
    {
        return $authUser->can('Replicate:Articulo');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Articulo');
    }

}