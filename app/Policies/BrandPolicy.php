<?php

namespace App\Policies;

use App\Models\Brand;
use App\Models\Seller;

class BrandPolicy extends BasePolicy
{
    /**
     * Determine whether the seller can view any models.
     */
    public function viewAny(Seller $seller): bool
    {
        return $seller->can('view_any_brand');
    }

    /**
     * Determine whether the seller can view the model.
     */
    public function view(Seller $seller, Brand $brand): bool
    {
        return $seller->can('view_brand');
    }

    /**
     * Determine whether the seller can create models.
     */
    public function create(Seller $seller): bool
    {
        return $seller->can('create_brand');
    }

    /**
     * Determine whether the seller can update the model.
     */
    public function update(Seller $seller, Brand $brand): bool
    {
        return $seller->can('update_brand');
    }

    /**
     * Determine whether the seller can delete the model.
     */
    public function delete(Seller $seller, Brand $brand): bool
    {
        return $seller->can('delete_brand');
    }

    /**
     * Determine whether the seller can bulk delete.
     */
    public function deleteAny(Seller $seller): bool
    {
        return $seller->can('delete_any_brand');
    }

    /**
     * Determine whether the seller can permanently delete.
     */
    public function forceDelete(Seller $seller, Brand $brand): bool
    {
        return $seller->can('force_delete_brand');
    }

    /**
     * Determine whether the seller can permanently bulk delete.
     */
    public function forceDeleteAny(Seller $seller): bool
    {
        return $seller->can('force_delete_any_brand');
    }

    /**
     * Determine whether the seller can restore.
     */
    public function restore(Seller $seller, Brand $brand): bool
    {
        return $seller->can('restore_brand');
    }

    /**
     * Determine whether the seller can bulk restore.
     */
    public function restoreAny(Seller $seller): bool
    {
        return $seller->can('restore_any_brand');
    }

    /**
     * Determine whether the seller can replicate.
     */
    public function replicate(Seller $seller, Brand $brand): bool
    {
        return $seller->can('replicate_brand');
    }

    /**
     * Determine whether the seller can reorder.
     */
    public function reorder(Seller $seller): bool
    {
        return $seller->can('reorder_brand');
    }
}
