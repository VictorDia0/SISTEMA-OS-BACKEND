<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface IUserService
{
    public function getAllUsers(): Collection;
    public function getUserById(string $id): User;
    public function getUserByEmail(string $email): User;
}
