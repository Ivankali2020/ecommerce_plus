<?php

namespace App\Policies;

use App\Models\OrderProduct;
use App\Models\Seller;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderProductPolicy
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
        return $seller->can('view_any_order::product');
    }

    /**
     * Determine whether the seller can view the model.
     */
    public function view(Seller $seller, OrderProduct $orderProduct): bool
    {
        return $seller->can('view_order::product');
    }

    /**
     * Determine whether the seller can create models.
     */
    public function create(Seller $seller): bool
    {
        return $seller->can('create_order::product');
    }

    /**
     * Determine whether the seller can update the model.
     */
    public function update(Seller $seller, OrderProduct $orderProduct): bool
    {
        return $seller->can('update_order::product');
    }

    /**
     * Determine whether the seller can delete the model.
     */
    public function delete(Seller $seller, OrderProduct $orderProduct): bool
    {
        return $seller->can('delete_order::product');
    }

    /**
     * Determine whether the seller can bulk delete.
     */
    public function deleteAny(Seller $seller): bool
    {
        return $seller->can('delete_any_order::product');
    }

    /**
     * Determine whether the seller can permanently delete.
     */
    public function forceDelete(Seller $seller, OrderProduct $orderProduct): bool
    {
        return $seller->can('force_delete_order::product');
    }

    /**
     * Determine whether the seller can permanently bulk delete.
     */
    public function forceDeleteAny(Seller $seller): bool
    {
        return $seller->can('force_delete_any_order::product');
    }

    /**
     * Determine whether the seller can restore.
     */
    public function restore(Seller $seller, OrderProduct $orderProduct): bool
    {
        return $seller->can('restore_order::product');
    }

    /**
     * Determine whether the seller can bulk restore.
     */
    public function restoreAny(Seller $seller): bool
    {
        return $seller->can('restore_any_order::product');
    }

    /**
     * Determine whether the seller can replicate.
     */
    public function replicate(Seller $seller, OrderProduct $orderProduct): bool
    {
        return $seller->can('replicate_order::product');
    }

    /**
     * Determine whether the seller can reorder.
     */
    public function reorder(Seller $seller): bool
    {
        return $seller->can('reorder_order::product');
    }
}
