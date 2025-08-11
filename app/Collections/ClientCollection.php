<?php

namespace App\Collections;

use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ClientCollection extends ResourceCollection
{
    public function __construct(Collection|array $resource)
    {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        return $this->collection
            ->map(function (Client $client) use ($request) {
                $cliente = ClientResource::make($client)->toArray($request);

                return $cliente;
            })
            ->toArray();
    }
}
