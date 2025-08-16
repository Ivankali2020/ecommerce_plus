<?php

namespace App\Policies;

use App\Models\Region;
use App\Models\Seller;

class RegionPolicy extends BasePolicy
{
    /**
     * Determine whether the seller can view any models.
     */
    public function viewAny(Seller $seller): bool
    {
        return $seller->can('view_any_region');
    }

    /**
     * Determine whether the seller can view the model.
     */
    public function view(Seller $seller, Region $region): bool
    {
        return $seller->can('view_region');
    }

    /**
     * Determine whether the seller can create models.
     */
    public function create(Seller $seller): bool
    {
        return $seller->can('create_region');
    }

    /**
     * Determine whether the seller can update the model.
     */
    public function update(Seller $seller, Region $region): bool
    {
        return $seller->can('update_region');
    }

    /**
     * Determine whether the seller can delete the model.
     */
    public function delete(Seller $seller, Region $region): bool
    {
        return $seller->can('delete_region');
    }

    /**
     * Determine whether the seller can bulk delete.
     */
    public function deleteAny(Seller $seller): bool
    {
        return $seller->can('delete_any_region');
    }

    /**
     * Determine whether the seller can permanently delete.
     */
    public function forceDelete(Seller $seller, Region $region): bool
    {
        return $seller->can('force_delete_region');
    }

    /**
     * Determine whether the seller can permanently bulk delete.
     */
    public function forceDeleteAny(Seller $seller): bool
    {
        return $seller->can('force_delete_any_region');
    }

    /**
     * Determine whether the seller can restore.
     */
    public function restore(Seller $seller, Region $region): bool
    {
        return $seller->can('restore_region');
    }

    /**
     * Determine whether the seller can bulk restore.
     */
    public function restoreAny(Seller $seller): bool
    {
        return $seller->can('restore_any_region');
    }

    /**
     * Determine whether the seller can replicate.
     */
    public function replicate(Seller $seller, Region $region): bool
    {
        return $seller->can('replicate_region');
    }

    /**
     * Determine whether the seller can reorder.
     */
    public function reorder(Seller $seller): bool
    {
        return $seller->can('reorder_region');
    }
}
