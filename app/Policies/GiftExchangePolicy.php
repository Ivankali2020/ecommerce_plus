<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\GiftExchange;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class GiftExchangePolicy extends BasePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:GiftExchange');
    }

    public function view(AuthUser $authUser, GiftExchange $giftExchange): bool
    {
        return $authUser->can('View:GiftExchange');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:GiftExchange');
    }

    public function update(AuthUser $authUser, GiftExchange $giftExchange): bool
    {
        return $authUser->can('Update:GiftExchange');
    }

    public function delete(AuthUser $authUser, GiftExchange $giftExchange): bool
    {
        return $authUser->can('Delete:GiftExchange');
    }

    public function restore(AuthUser $authUser, GiftExchange $giftExchange): bool
    {
        return $authUser->can('Restore:GiftExchange');
    }

    public function forceDelete(AuthUser $authUser, GiftExchange $giftExchange): bool
    {
        return $authUser->can('ForceDelete:GiftExchange');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:GiftExchange');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:GiftExchange');
    }

    public function replicate(AuthUser $authUser, GiftExchange $giftExchange): bool
    {
        return $authUser->can('Replicate:GiftExchange');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:GiftExchange');
    }
}
