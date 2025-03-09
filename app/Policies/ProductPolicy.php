<?php

namespace App\Policies;

use App\Models\Seller;
use App\Models\Product;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    public function before(FilamentUser $user, $ability)
    {
        if (auth()->guard('admin')->check()) {
            return true;
        }
    }
    /**
     * Determine whether the seller can view any models.
     */
    public function viewAny(Seller $seller): bool
    {
        return $seller->can('view_any_product');
    }

    /**
     * Determine whether the seller can view the model.
     */
    public function view(Seller $seller, Product $product): bool
    {
        return $seller->can('view_product');
    }

    /**
     * Determine whether the seller can create models.
     */
    public function create(Seller $seller): bool
    {
        return $seller->can('create_product');
    }

    /**
     * Determine whether the seller can update the model.
     */
    public function update(Seller $seller, Product $product): bool
    {
        return $seller->can('update_product');
    }

    /**
     * Determine whether the seller can delete the model.
     */
    public function delete(Seller $seller, Product $product): bool
    {
        return $seller->can('delete_product');
    }

    /**
     * Determine whether the seller can bulk delete.
     */
    public function deleteAny(Seller $seller): bool
    {
        return $seller->can('delete_any_product');
    }

    /**
     * Determine whether the seller can permanently delete.
     */
    public function forceDelete(Seller $seller, Product $product): bool
    {
        return $seller->can('force_delete_product');
    }

    /**
     * Determine whether the seller can permanently bulk delete.
     */
    public function forceDeleteAny(Seller $seller): bool
    {
        return $seller->can('force_delete_any_product');
    }

    /**
     * Determine whether the seller can restore.
     */
    public function restore(Seller $seller, Product $product): bool
    {
        return $seller->can('restore_product');
    }

    /**
     * Determine whether the seller can bulk restore.
     */
    public function restoreAny(Seller $seller): bool
    {
        return $seller->can('restore_any_product');
    }

    /**
     * Determine whether the seller can replicate.
     */
    public function replicate(Seller $seller, Product $product): bool
    {
        return $seller->can('replicate_product');
    }

    /**
     * Determine whether the seller can reorder.
     */
    public function reorder(Seller $seller): bool
    {
        return $seller->can('reorder_product');
    }
}
