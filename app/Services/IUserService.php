<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

interface IUserService
{
    public function getAllUsers(): Collection;
    //public function getAllOrdersByUser(User $user, object $data): SupportCollection;
    public function GetUserByEmail(string $email): User;
}
