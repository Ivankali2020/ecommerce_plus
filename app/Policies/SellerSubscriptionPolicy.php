<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\SellerSubscription;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class SellerSubscriptionPolicy extends BasePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SellerSubscription');
    }

    public function view(AuthUser $authUser, SellerSubscription $sellerSubscription): bool
    {
        return $authUser->can('View:SellerSubscription');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SellerSubscription');
    }

    public function update(AuthUser $authUser, SellerSubscription $sellerSubscription): bool
    {
        return $authUser->can('Update:SellerSubscription');
    }

    public function delete(AuthUser $authUser, SellerSubscription $sellerSubscription): bool
    {
        return $authUser->can('Delete:SellerSubscription');
    }

    public function restore(AuthUser $authUser, SellerSubscription $sellerSubscription): bool
    {
        return $authUser->can('Restore:SellerSubscription');
    }

    public function forceDelete(AuthUser $authUser, SellerSubscription $sellerSubscription): bool
    {
        return $authUser->can('ForceDelete:SellerSubscription');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SellerSubscription');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SellerSubscription');
    }

    public function replicate(AuthUser $authUser, SellerSubscription $sellerSubscription): bool
    {
        return $authUser->can('Replicate:SellerSubscription');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SellerSubscription');
    }
}
