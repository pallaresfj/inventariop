<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CargoParroquial;
use Illuminate\Auth\Access\HandlesAuthorization;

class CargoParroquialPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CargoParroquial');
    }

    public function view(AuthUser $authUser, CargoParroquial $cargoParroquial): bool
    {
        return $authUser->can('View:CargoParroquial');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CargoParroquial');
    }

    public function update(AuthUser $authUser, CargoParroquial $cargoParroquial): bool
    {
        return $authUser->can('Update:CargoParroquial');
    }

    public function delete(AuthUser $authUser, CargoParroquial $cargoParroquial): bool
    {
        return $authUser->can('Delete:CargoParroquial');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CargoParroquial');
    }

    public function restore(AuthUser $authUser, CargoParroquial $cargoParroquial): bool
    {
        return $authUser->can('Restore:CargoParroquial');
    }

    public function forceDelete(AuthUser $authUser, CargoParroquial $cargoParroquial): bool
    {
        return $authUser->can('ForceDelete:CargoParroquial');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CargoParroquial');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CargoParroquial');
    }

    public function replicate(AuthUser $authUser, CargoParroquial $cargoParroquial): bool
    {
        return $authUser->can('Replicate:CargoParroquial');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CargoParroquial');
    }

}