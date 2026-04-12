<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TituloSacerdotal;
use Illuminate\Auth\Access\HandlesAuthorization;

class TituloSacerdotalPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TituloSacerdotal');
    }

    public function view(AuthUser $authUser, TituloSacerdotal $tituloSacerdotal): bool
    {
        return $authUser->can('View:TituloSacerdotal');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TituloSacerdotal');
    }

    public function update(AuthUser $authUser, TituloSacerdotal $tituloSacerdotal): bool
    {
        return $authUser->can('Update:TituloSacerdotal');
    }

    public function delete(AuthUser $authUser, TituloSacerdotal $tituloSacerdotal): bool
    {
        return $authUser->can('Delete:TituloSacerdotal');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TituloSacerdotal');
    }

    public function restore(AuthUser $authUser, TituloSacerdotal $tituloSacerdotal): bool
    {
        return $authUser->can('Restore:TituloSacerdotal');
    }

    public function forceDelete(AuthUser $authUser, TituloSacerdotal $tituloSacerdotal): bool
    {
        return $authUser->can('ForceDelete:TituloSacerdotal');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TituloSacerdotal');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TituloSacerdotal');
    }

    public function replicate(AuthUser $authUser, TituloSacerdotal $tituloSacerdotal): bool
    {
        return $authUser->can('Replicate:TituloSacerdotal');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TituloSacerdotal');
    }

}