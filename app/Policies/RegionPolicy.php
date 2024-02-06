<?php

namespace App\Policies;

use App\Models\FilamentUser;
use App\Models\Region;
use Illuminate\Auth\Access\HandlesAuthorization;

class RegionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the seller can view any models.
     *
     * @param  \App\Models\FilamentUser  $seller
     * @return bool
     */
    public function viewAny(\Filament\Models\Contracts\FilamentUser $seller): bool
    {
        return $seller->can('view_any_region');
    }

    /**
     * Determine whether the seller can view the model.
     *
     * @param  \App\Models\FilamentUser  $seller
     * @param  \App\Models\Region  $region
     * @return bool
     */
    public function view(\Filament\Models\Contracts\FilamentUser $seller, Region $region): bool
    {
        return $seller->can('view_region');
    }

    /**
     * Determine whether the seller can create models.
     *
     * @param  \App\Models\FilamentUser  $seller
     * @return bool
     */
    public function create(\Filament\Models\Contracts\FilamentUser $seller): bool
    {
        return $seller->can('create_region');
    }

    /**
     * Determine whether the seller can update the model.
     *
     * @param  \App\Models\FilamentUser  $seller
     * @param  \App\Models\Region  $region
     * @return bool
     */
    public function update(\Filament\Models\Contracts\FilamentUser $seller, Region $region): bool
    {
        return $seller->can('update_region');
    }

    /**
     * Determine whether the seller can delete the model.
     *
     * @param  \App\Models\FilamentUser  $seller
     * @param  \App\Models\Region  $region
     * @return bool
     */
    public function delete(\Filament\Models\Contracts\FilamentUser $seller, Region $region): bool
    {
        return $seller->can('delete_region');
    }

    /**
     * Determine whether the seller can bulk delete.
     *
     * @param  \App\Models\FilamentUser  $seller
     * @return bool
     */
    public function deleteAny(\Filament\Models\Contracts\FilamentUser $seller): bool
    {
        return $seller->can('delete_any_region');
    }

    /**
     * Determine whether the seller can permanently delete.
     *
     * @param  \App\Models\FilamentUser  $seller
     * @param  \App\Models\Region  $region
     * @return bool
     */
    public function forceDelete(\Filament\Models\Contracts\FilamentUser $seller, Region $region): bool
    {
        return $seller->can('force_delete_region');
    }

    /**
     * Determine whether the seller can permanently bulk delete.
     *
     * @param  \App\Models\FilamentUser  $seller
     * @return bool
     */
    public function forceDeleteAny(\Filament\Models\Contracts\FilamentUser $seller): bool
    {
        return $seller->can('force_delete_any_region');
    }

    /**
     * Determine whether the seller can restore.
     *
     * @param  \App\Models\FilamentUser  $seller
     * @param  \App\Models\Region  $region
     * @return bool
     */
    public function restore(\Filament\Models\Contracts\FilamentUser $seller, Region $region): bool
    {
        return $seller->can('restore_region');
    }

    /**
     * Determine whether the seller can bulk restore.
     *
     * @param  \App\Models\FilamentUser  $seller
     * @return bool
     */
    public function restoreAny(\Filament\Models\Contracts\FilamentUser $seller): bool
    {
        return $seller->can('restore_any_region');
    }

    /**
     * Determine whether the seller can replicate.
     *
     * @param  \App\Models\FilamentUser  $seller
     * @param  \App\Models\Region  $region
     * @return bool
     */
    public function replicate(\Filament\Models\Contracts\FilamentUser $seller, Region $region): bool
    {
        return $seller->can('replicate_region');
    }

    /**
     * Determine whether the seller can reorder.
     *
     * @param  \App\Models\FilamentUser  $seller
     * @return bool
     */
    public function reorder(\Filament\Models\Contracts\FilamentUser $seller): bool
    {
        return $seller->can('reorder_region');
    }

}
