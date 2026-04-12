<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Deanery;
use Illuminate\Auth\Access\HandlesAuthorization;

class DeaneryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Deanery');
    }

    public function view(AuthUser $authUser, Deanery $deanery): bool
    {
        return $authUser->can('View:Deanery');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Deanery');
    }

    public function update(AuthUser $authUser, Deanery $deanery): bool
    {
        return $authUser->can('Update:Deanery');
    }

    public function delete(AuthUser $authUser, Deanery $deanery): bool
    {
        return $authUser->can('Delete:Deanery');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Deanery');
    }

    public function restore(AuthUser $authUser, Deanery $deanery): bool
    {
        return $authUser->can('Restore:Deanery');
    }

    public function forceDelete(AuthUser $authUser, Deanery $deanery): bool
    {
        return $authUser->can('ForceDelete:Deanery');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Deanery');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Deanery');
    }

    public function replicate(AuthUser $authUser, Deanery $deanery): bool
    {
        return $authUser->can('Replicate:Deanery');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Deanery');
    }

}