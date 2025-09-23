<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Gift;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class GiftPolicy extends BasePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Gift');
    }

    public function view(AuthUser $authUser, Gift $gift): bool
    {
        return $authUser->can('View:Gift');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Gift');
    }

    public function update(AuthUser $authUser, Gift $gift): bool
    {
        return $authUser->can('Update:Gift');
    }

    public function delete(AuthUser $authUser, Gift $gift): bool
    {
        return $authUser->can('Delete:Gift');
    }

    public function restore(AuthUser $authUser, Gift $gift): bool
    {
        return $authUser->can('Restore:Gift');
    }

    public function forceDelete(AuthUser $authUser, Gift $gift): bool
    {
        return $authUser->can('ForceDelete:Gift');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Gift');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Gift');
    }

    public function replicate(AuthUser $authUser, Gift $gift): bool
    {
        return $authUser->can('Replicate:Gift');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Gift');
    }
}
