<?php

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use HandlesAuthorization;

    public function getAllUsers(User $user): Response
    {
        $hasPermission = Response::deny();

        if ($user->hasPermissionTo(PermissionEnum::VIEW_ADMIN)) {
            $hasPermission = Response::allow();
        }
        return $hasPermission;
    }
}
