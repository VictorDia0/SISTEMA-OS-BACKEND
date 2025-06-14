<?php

namespace App\Collections;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
    public function __construct(Collection|array $resource)
    {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        return $this->collection
            ->map(function (User $user) use ($request) {
                $usuario = UserResource::make($user)->toArray($request);
                $usuario['status'] = $user->hasVerifiedEmail() ? 'Ativo' : 'Pendente';
                $usuario['role'] = $this->getRole($user);
                return $usuario;
            })
            ->toArray();
    }
}
