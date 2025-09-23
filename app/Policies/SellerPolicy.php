<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class SellerPolicy extends BasePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Seller');
    }

    public function view(AuthUser $authUser): bool
    {
        return $authUser->can('View:Seller');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Seller');
    }

    public function update(AuthUser $authUser): bool
    {
        return $authUser->can('Update:Seller');
    }

    public function delete(AuthUser $authUser): bool
    {
        return $authUser->can('Delete:Seller');
    }

    public function restore(AuthUser $authUser): bool
    {
        return $authUser->can('Restore:Seller');
    }

    public function forceDelete(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDelete:Seller');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Seller');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Seller');
    }

    public function replicate(AuthUser $authUser): bool
    {
        return $authUser->can('Replicate:Seller');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Seller');
    }
}
