<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Arciprestazgo;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArciprestazgoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Arciprestazgo');
    }

    public function view(AuthUser $authUser, Arciprestazgo $arciprestazgo): bool
    {
        return $authUser->can('View:Arciprestazgo');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Arciprestazgo');
    }

    public function update(AuthUser $authUser, Arciprestazgo $arciprestazgo): bool
    {
        return $authUser->can('Update:Arciprestazgo');
    }

    public function delete(AuthUser $authUser, Arciprestazgo $arciprestazgo): bool
    {
        return $authUser->can('Delete:Arciprestazgo');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Arciprestazgo');
    }

    public function restore(AuthUser $authUser, Arciprestazgo $arciprestazgo): bool
    {
        return $authUser->can('Restore:Arciprestazgo');
    }

    public function forceDelete(AuthUser $authUser, Arciprestazgo $arciprestazgo): bool
    {
        return $authUser->can('ForceDelete:Arciprestazgo');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Arciprestazgo');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Arciprestazgo');
    }

    public function replicate(AuthUser $authUser, Arciprestazgo $arciprestazgo): bool
    {
        return $authUser->can('Replicate:Arciprestazgo');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Arciprestazgo');
    }

}