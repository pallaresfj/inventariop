<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Community;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommunityPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Community');
    }

    public function view(AuthUser $authUser, Community $community): bool
    {
        return $authUser->can('View:Community');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Community');
    }

    public function update(AuthUser $authUser, Community $community): bool
    {
        return $authUser->can('Update:Community');
    }

    public function delete(AuthUser $authUser, Community $community): bool
    {
        return $authUser->can('Delete:Community');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Community');
    }

    public function restore(AuthUser $authUser, Community $community): bool
    {
        return $authUser->can('Restore:Community');
    }

    public function forceDelete(AuthUser $authUser, Community $community): bool
    {
        return $authUser->can('ForceDelete:Community');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Community');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Community');
    }

    public function replicate(AuthUser $authUser, Community $community): bool
    {
        return $authUser->can('Replicate:Community');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Community');
    }

}