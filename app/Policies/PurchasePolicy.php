<?php

namespace App\Policies;

use App\Models\Seller;
use App\Models\Purchase;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchasePolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the seller can view any models.
     */
    public function viewAny(Seller $seller): bool
    {
        return $seller->can('view_any_purchases::purchase');
    }

    /**
     * Determine whether the seller can view the model.
     */
    public function view(Seller $seller, Purchase $purchase): bool
    {
        return $seller->can('view_purchases::purchase');
    }

    /**
     * Determine whether the seller can create models.
     */
    public function create(Seller $seller): bool
    {
        return $seller->can('create_purchases::purchase');
    }

    /**
     * Determine whether the seller can update the model.
     */
    public function update(Seller $seller, Purchase $purchase): bool
    {
        return $seller->can('update_purchases::purchase');
    }

    /**
     * Determine whether the seller can delete the model.
     */
    public function delete(Seller $seller, Purchase $purchase): bool
    {
        return $seller->can('delete_purchases::purchase');
    }

    /**
     * Determine whether the seller can bulk delete.
     */
    public function deleteAny(Seller $seller): bool
    {
        return $seller->can('delete_any_purchases::purchase');
    }

    /**
     * Determine whether the seller can permanently delete.
     */
    public function forceDelete(Seller $seller, Purchase $purchase): bool
    {
        return $seller->can('force_delete_purchases::purchase');
    }

    /**
     * Determine whether the seller can permanently bulk delete.
     */
    public function forceDeleteAny(Seller $seller): bool
    {
        return $seller->can('force_delete_any_purchases::purchase');
    }

    /**
     * Determine whether the seller can restore.
     */
    public function restore(Seller $seller, Purchase $purchase): bool
    {
        return $seller->can('restore_purchases::purchase');
    }

    /**
     * Determine whether the seller can bulk restore.
     */
    public function restoreAny(Seller $seller): bool
    {
        return $seller->can('restore_any_purchases::purchase');
    }

    /**
     * Determine whether the seller can replicate.
     */
    public function replicate(Seller $seller, Purchase $purchase): bool
    {
        return $seller->can('replicate_purchases::purchase');
    }

    /**
     * Determine whether the seller can reorder.
     */
    public function reorder(Seller $seller): bool
    {
        return $seller->can('reorder_purchases::purchase');
    }
}
