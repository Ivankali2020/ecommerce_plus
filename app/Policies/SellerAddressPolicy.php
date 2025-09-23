<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\SellerAddress;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class SellerAddressPolicy extends BasePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SellerAddress');
    }

    public function view(AuthUser $authUser, SellerAddress $sellerAddress): bool
    {
        return $authUser->can('View:SellerAddress');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SellerAddress');
    }

    public function update(AuthUser $authUser, SellerAddress $sellerAddress): bool
    {
        return $authUser->can('Update:SellerAddress');
    }

    public function delete(AuthUser $authUser, SellerAddress $sellerAddress): bool
    {
        return $authUser->can('Delete:SellerAddress');
    }

    public function restore(AuthUser $authUser, SellerAddress $sellerAddress): bool
    {
        return $authUser->can('Restore:SellerAddress');
    }

    public function forceDelete(AuthUser $authUser, SellerAddress $sellerAddress): bool
    {
        return $authUser->can('ForceDelete:SellerAddress');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SellerAddress');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SellerAddress');
    }

    public function replicate(AuthUser $authUser, SellerAddress $sellerAddress): bool
    {
        return $authUser->can('Replicate:SellerAddress');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SellerAddress');
    }
}
