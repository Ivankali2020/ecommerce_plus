<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\UserAccountService;
use App\Traits\ResponseTraits;

class UserAccountController extends Controller
{
    use ResponseTraits;

    /**
     * @var UserAuthService
     */
    private UserAccountService $userAccountService;

    public function __construct(UserAccountService $userAccountService)
    {
        $this->userAccountService = $userAccountService;
    }

    public function getMyAccount()
    {
        $user = $this->userAccountService->getMyAccount();

        return $this->success('Get account successful', new UserResource($user));
    }

    public function deleteAccount()
    {
        $this->userAccountService->deleteAccount();

        return $this->success('Account deleted successfully');
    }

    // TODO::DeleteUser

    // TODO::ChangePassword

    // TODO::StoreFirebaseToken
}
