<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Exchange;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ExchangePolicy extends BasePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Exchange');
    }

    public function view(AuthUser $authUser, Exchange $exchange): bool
    {
        return $authUser->can('View:Exchange');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Exchange');
    }

    public function update(AuthUser $authUser, Exchange $exchange): bool
    {
        return $authUser->can('Update:Exchange');
    }

    public function delete(AuthUser $authUser, Exchange $exchange): bool
    {
        return $authUser->can('Delete:Exchange');
    }

    public function restore(AuthUser $authUser, Exchange $exchange): bool
    {
        return $authUser->can('Restore:Exchange');
    }

    public function forceDelete(AuthUser $authUser, Exchange $exchange): bool
    {
        return $authUser->can('ForceDelete:Exchange');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Exchange');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Exchange');
    }

    public function replicate(AuthUser $authUser, Exchange $exchange): bool
    {
        return $authUser->can('Replicate:Exchange');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Exchange');
    }
}
