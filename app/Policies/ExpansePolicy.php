<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Expanse;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ExpansePolicy extends BasePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Expanse');
    }

    public function view(AuthUser $authUser, Expanse $expanse): bool
    {
        return $authUser->can('View:Expanse');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Expanse');
    }

    public function update(AuthUser $authUser, Expanse $expanse): bool
    {
        return $authUser->can('Update:Expanse');
    }

    public function delete(AuthUser $authUser, Expanse $expanse): bool
    {
        return $authUser->can('Delete:Expanse');
    }

    public function restore(AuthUser $authUser, Expanse $expanse): bool
    {
        return $authUser->can('Restore:Expanse');
    }

    public function forceDelete(AuthUser $authUser, Expanse $expanse): bool
    {
        return $authUser->can('ForceDelete:Expanse');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Expanse');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Expanse');
    }

    public function replicate(AuthUser $authUser, Expanse $expanse): bool
    {
        return $authUser->can('Replicate:Expanse');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Expanse');
    }
}
