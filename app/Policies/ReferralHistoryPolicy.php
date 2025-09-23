<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ReferralHistory;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ReferralHistoryPolicy extends BasePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ReferralHistory');
    }

    public function view(AuthUser $authUser, ReferralHistory $referralHistory): bool
    {
        return $authUser->can('View:ReferralHistory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ReferralHistory');
    }

    public function update(AuthUser $authUser, ReferralHistory $referralHistory): bool
    {
        return $authUser->can('Update:ReferralHistory');
    }

    public function delete(AuthUser $authUser, ReferralHistory $referralHistory): bool
    {
        return $authUser->can('Delete:ReferralHistory');
    }

    public function restore(AuthUser $authUser, ReferralHistory $referralHistory): bool
    {
        return $authUser->can('Restore:ReferralHistory');
    }

    public function forceDelete(AuthUser $authUser, ReferralHistory $referralHistory): bool
    {
        return $authUser->can('ForceDelete:ReferralHistory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ReferralHistory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ReferralHistory');
    }

    public function replicate(AuthUser $authUser, ReferralHistory $referralHistory): bool
    {
        return $authUser->can('Replicate:ReferralHistory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ReferralHistory');
    }
}
