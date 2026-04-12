<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Restauracion;
use Illuminate\Auth\Access\HandlesAuthorization;

class RestauracionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Restauracion');
    }

    public function view(AuthUser $authUser, Restauracion $restauracion): bool
    {
        return $authUser->can('View:Restauracion');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Restauracion');
    }

    public function update(AuthUser $authUser, Restauracion $restauracion): bool
    {
        return $authUser->can('Update:Restauracion');
    }

    public function delete(AuthUser $authUser, Restauracion $restauracion): bool
    {
        return $authUser->can('Delete:Restauracion');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Restauracion');
    }

    public function restore(AuthUser $authUser, Restauracion $restauracion): bool
    {
        return $authUser->can('Restore:Restauracion');
    }

    public function forceDelete(AuthUser $authUser, Restauracion $restauracion): bool
    {
        return $authUser->can('ForceDelete:Restauracion');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Restauracion');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Restauracion');
    }

    public function replicate(AuthUser $authUser, Restauracion $restauracion): bool
    {
        return $authUser->can('Replicate:Restauracion');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Restauracion');
    }

}