<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;

class ClientService
{
    public function getAllClients(): Collection
    {
        return Client::all();
    }
}
