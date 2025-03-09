<?php

namespace App\Policies;

use App\Models\Seller;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class SellerPolicy
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
     *
     * @param  \App\Models\Seller  $seller
     * @return bool
     */
    public function viewAny(Seller $seller): bool
    {
        return $seller->can('view_any_staff');
    }

    /**
     * Determine whether the seller can view the model.
     *
     * @param  \App\Models\Seller  $seller
     * @return bool
     */
    public function view(Seller $seller): bool
    {
        return $seller->can('view_staff');
    }

    /**
     * Determine whether the seller can create models.
     *
     * @param  \App\Models\Seller  $seller
     * @return bool
     */
    public function create(Seller $seller): bool
    {
        return $seller->can('create_staff');
    }

    /**
     * Determine whether the seller can update the model.
     *
     * @param  \App\Models\Seller  $seller
     * @return bool
     */
    public function update(Seller $seller): bool
    {
        return $seller->can('update_staff');
    }

    /**
     * Determine whether the seller can delete the model.
     *
     * @param  \App\Models\Seller  $seller
     * @return bool
     */
    public function delete(Seller $seller): bool
    {
        return $seller->can('delete_staff');
    }

    /**
     * Determine whether the seller can bulk delete.
     *
     * @param  \App\Models\Seller  $seller
     * @return bool
     */
    public function deleteAny(Seller $seller): bool
    {
        return $seller->can('delete_any_staff');
    }

    /**
     * Determine whether the seller can permanently delete.
     *
     * @param  \App\Models\Seller  $seller
     * @return bool
     */
    public function forceDelete(Seller $seller): bool
    {
        return $seller->can('force_delete_staff');
    }

    /**
     * Determine whether the seller can permanently bulk delete.
     *
     * @param  \App\Models\Seller  $seller
     * @return bool
     */
    public function forceDeleteAny(Seller $seller): bool
    {
        return $seller->can('force_delete_any_staff');
    }

    /**
     * Determine whether the seller can restore.
     *
     * @param  \App\Models\Seller  $seller
     * @return bool
     */
    public function restore(Seller $seller): bool
    {
        return $seller->can('restore_staff');
    }

    /**
     * Determine whether the seller can bulk restore.
     *
     * @param  \App\Models\Seller  $seller
     * @return bool
     */
    public function restoreAny(Seller $seller): bool
    {
        return $seller->can('restore_any_staff');
    }

    /**
     * Determine whether the seller can bulk restore.
     *
     * @param  \App\Models\Seller  $seller
     * @return bool
     */
    public function replicate(Seller $seller): bool
    {
        return $seller->can('replicate_staff');
    }

    /**
     * Determine whether the seller can reorder.
     *
     * @param  \App\Models\Seller  $seller
     * @return bool
     */
    public function reorder(Seller $seller): bool
    {
        return $seller->can('reorder_staff');
    }
}
