<?php

namespace App\Policies;

use App\Models\Expanse;
use App\Models\Seller;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExpansePolicy
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
        return $seller->can('view_any_expanse');
    }

    /**
     * Determine whether the seller can view the model.
     */
    public function view(Seller $seller, Expanse $expanse): bool
    {
        return $seller->can('view_expanse');
    }

    /**
     * Determine whether the seller can create models.
     */
    public function create(Seller $seller): bool
    {
        return $seller->can('create_expanse');
    }

    /**
     * Determine whether the seller can update the model.
     */
    public function update(Seller $seller, Expanse $expanse): bool
    {
        return $seller->can('update_expanse');
    }

    /**
     * Determine whether the seller can delete the model.
     */
    public function delete(Seller $seller, Expanse $expanse): bool
    {
        return $seller->can('delete_expanse');
    }

    /**
     * Determine whether the seller can bulk delete.
     */
    public function deleteAny(Seller $seller): bool
    {
        return $seller->can('delete_any_expanse');
    }

    /**
     * Determine whether the seller can permanently delete.
     */
    public function forceDelete(Seller $seller, Expanse $expanse): bool
    {
        return $seller->can('force_delete_expanse');
    }

    /**
     * Determine whether the seller can permanently bulk delete.
     */
    public function forceDeleteAny(Seller $seller): bool
    {
        return $seller->can('force_delete_any_expanse');
    }

    /**
     * Determine whether the seller can restore.
     */
    public function restore(Seller $seller, Expanse $expanse): bool
    {
        return $seller->can('restore_expanse');
    }

    /**
     * Determine whether the seller can bulk restore.
     */
    public function restoreAny(Seller $seller): bool
    {
        return $seller->can('restore_any_expanse');
    }

    /**
     * Determine whether the seller can replicate.
     */
    public function replicate(Seller $seller, Expanse $expanse): bool
    {
        return $seller->can('replicate_expanse');
    }

    /**
     * Determine whether the seller can reorder.
     */
    public function reorder(Seller $seller): bool
    {
        return $seller->can('reorder_expanse');
    }
}
