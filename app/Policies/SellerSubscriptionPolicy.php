<?php

namespace App\Policies;

use App\Models\Seller;
use App\Models\SellerSubscription;
use Illuminate\Auth\Access\HandlesAuthorization;

class SellerSubscriptionPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the seller can view any models.
     */
    public function viewAny(Seller $seller): bool
    {
        return $seller->can('view_any_seller::subscriptions::seller::subscription');
    }

    /**
     * Determine whether the seller can view the model.
     */
    public function view(Seller $seller, SellerSubscription $sellerSubscription): bool
    {
        return $seller->can('view_seller::subscriptions::seller::subscription');
    }

    /**
     * Determine whether the seller can create models.
     */
    public function create(Seller $seller): bool
    {
        return $seller->can('create_seller::subscriptions::seller::subscription');
    }

    /**
     * Determine whether the seller can update the model.
     */
    public function update(Seller $seller, SellerSubscription $sellerSubscription): bool
    {
        return $seller->can('update_seller::subscriptions::seller::subscription');
    }

    /**
     * Determine whether the seller can delete the model.
     */
    public function delete(Seller $seller, SellerSubscription $sellerSubscription): bool
    {
        return $seller->can('delete_seller::subscriptions::seller::subscription');
    }

    /**
     * Determine whether the seller can bulk delete.
     */
    public function deleteAny(Seller $seller): bool
    {
        return $seller->can('delete_any_seller::subscriptions::seller::subscription');
    }

    /**
     * Determine whether the seller can permanently delete.
     */
    public function forceDelete(Seller $seller, SellerSubscription $sellerSubscription): bool
    {
        return $seller->can('force_delete_seller::subscriptions::seller::subscription');
    }

    /**
     * Determine whether the seller can permanently bulk delete.
     */
    public function forceDeleteAny(Seller $seller): bool
    {
        return $seller->can('force_delete_any_seller::subscriptions::seller::subscription');
    }

    /**
     * Determine whether the seller can restore.
     */
    public function restore(Seller $seller, SellerSubscription $sellerSubscription): bool
    {
        return $seller->can('restore_seller::subscriptions::seller::subscription');
    }

    /**
     * Determine whether the seller can bulk restore.
     */
    public function restoreAny(Seller $seller): bool
    {
        return $seller->can('restore_any_seller::subscriptions::seller::subscription');
    }

    /**
     * Determine whether the seller can replicate.
     */
    public function replicate(Seller $seller, SellerSubscription $sellerSubscription): bool
    {
        return $seller->can('replicate_seller::subscriptions::seller::subscription');
    }

    /**
     * Determine whether the seller can reorder.
     */
    public function reorder(Seller $seller): bool
    {
        return $seller->can('reorder_seller::subscriptions::seller::subscription');
    }
}
