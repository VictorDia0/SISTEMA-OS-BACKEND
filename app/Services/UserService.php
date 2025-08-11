<?php

namespace App\Services;

use App\Exceptions\UserNotFoundException;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserService implements IUserService
{
    public function __construct() {}

    public function getAllUsers(): Collection
    {
        return User::all();
    }

    public function getUserById(string $id): User
    {
        $user = User::getUserByIdOrFail($id);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    public function getUserByEmail(string $email): User
    {
        $user = User::getUserByEmail($email);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }
}
