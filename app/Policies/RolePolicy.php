<?php

namespace App\Policies;

use App\Models\Seller;
use Spatie\Permission\Models\Role;

class RolePolicy extends BasePolicy
{
    /**
     * Determine whether the seller can view any models.
     */
    public function viewAny(Seller $seller): bool
    {
        return $seller->can('view_any_role');
    }

    /**
     * Determine whether the seller can view the model.
     */
    public function view(Seller $seller, Role $role): bool
    {
        return $seller->can('view_role');
    }

    /**
     * Determine whether the seller can create models.
     */
    public function create(Seller $seller): bool
    {
        return $seller->can('create_role');
    }

    /**
     * Determine whether the seller can update the model.
     */
    public function update(Seller $seller, Role $role): bool
    {
        return $seller->can('update_role');
    }

    /**
     * Determine whether the seller can delete the model.
     */
    public function delete(Seller $seller, Role $role): bool
    {
        return $seller->can('delete_role');
    }

    /**
     * Determine whether the seller can bulk delete.
     */
    public function deleteAny(Seller $seller): bool
    {
        return $seller->can('delete_any_role');
    }

    /**
     * Determine whether the seller can permanently delete.
     */
    public function forceDelete(Seller $seller, Role $role): bool
    {
        return $seller->can('{{ ForceDelete }}');
    }

    /**
     * Determine whether the seller can permanently bulk delete.
     */
    public function forceDeleteAny(Seller $seller): bool
    {
        return $seller->can('{{ ForceDeleteAny }}');
    }

    /**
     * Determine whether the seller can restore.
     */
    public function restore(Seller $seller, Role $role): bool
    {
        return $seller->can('{{ Restore }}');
    }

    /**
     * Determine whether the seller can bulk restore.
     */
    public function restoreAny(Seller $seller): bool
    {
        return $seller->can('{{ RestoreAny }}');
    }

    /**
     * Determine whether the seller can replicate.
     */
    public function replicate(Seller $seller, Role $role): bool
    {
        return $seller->can('{{ Replicate }}');
    }

    /**
     * Determine whether the seller can reorder.
     */
    public function reorder(Seller $seller): bool
    {
        return $seller->can('{{ Reorder }}');
    }
}
