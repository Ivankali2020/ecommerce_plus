<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Township;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class TownshipPolicy extends BasePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Township');
    }

    public function view(AuthUser $authUser, Township $township): bool
    {
        return $authUser->can('View:Township');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Township');
    }

    public function update(AuthUser $authUser, Township $township): bool
    {
        return $authUser->can('Update:Township');
    }

    public function delete(AuthUser $authUser, Township $township): bool
    {
        return $authUser->can('Delete:Township');
    }

    public function restore(AuthUser $authUser, Township $township): bool
    {
        return $authUser->can('Restore:Township');
    }

    public function forceDelete(AuthUser $authUser, Township $township): bool
    {
        return $authUser->can('ForceDelete:Township');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Township');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Township');
    }

    public function replicate(AuthUser $authUser, Township $township): bool
    {
        return $authUser->can('Replicate:Township');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Township');
    }
}
