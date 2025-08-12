<?php

namespace App\Models\Traits;

use App\Enums\RoleEnum;
use App\Exceptions\PermissoesException;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Contracts\Permission;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Http\Response;

trait HasRolesWithContext
{
    use HasRoles {
        HasRoles::roles as protected spatieRoles;
        HasRoles::hasRole as protected spatieHasRole;
        HasRoles::removeRole as protected spatieRemoveRole;
        HasRoles::permissions as protected spatiePermissions;
        HasRoles::hasPermissionTo as protected spatieHasPermissionTo;
    }

    public function rolesEvento(int $evento_id): BelongsToMany
    {
        $roles = $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id')->wherePivot(
            'evento_id',
            $evento_id
        );

        return $roles;
    }

    public function rolesWithNullEvento()
    {
        return $this->spatieRoles()->wherePivot('evento_id', null);
    }

    public function hasRole($roles, int $evento_id = null, ?string $guard = null): bool
    {
        if ($evento_id == null) {
            return $this->spatieHasRole($roles, $guard);
        }

        if (is_string($roles) && strpos($roles, '|') !== false) {
            $roles = $this->convertPipeToArray($roles);
        }

        if ($roles instanceof \BackedEnum) {
            $roles = $roles->value;
        }

        $rolesEvento = $this->rolesEvento($evento_id)->get();

        if (is_int($roles) || PermissionRegistrar::isUid($roles)) {
            $key = (new ($this->getRoleClass())())->getKeyName();
            return $rolesEvento->contains($key, $roles);
        }

        if (is_string($roles)) {
            return $rolesEvento->contains('name', $roles);
        }

        if ($roles instanceof Role) {
            return $rolesEvento->contains($roles->getKeyName(), $roles->getKey());
        }

        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role, $evento_id)) {
                    return true;
                }
            }

            return false;
        }

        if ($roles instanceof Collection) {
            return $roles->intersect($this->roles->wherePivot('evento_id', $evento_id))->isNotEmpty();
        }

        throw new \TypeError('Unsupported type for $roles parameter to hasRole().');
    }

    private function spatieAssignRole(...$roles): static
    {
        $roles = $this->collectRoles($roles);

        $model = $this->getModel();

        foreach ($roles as $role) {
            $query = $this->spatieRoles()
                ->where('role_id', $role)
                ->whereNull('evento_id');

            if ($query->exists()) {
                throw new PermissoesException('O papel já foi atribuído ao usuário!', Response::HTTP_CONFLICT);
            }
        }

        if ($model->exists) {
            $this->spatieRoles()->attach($roles);
            $model->unsetRelation('roles');
        } else {
            $class = \get_class($model);

            $class::saved(function ($object) use ($roles, $model) {
                if ($model->getKey() != $object->getKey()) {
                    return;
                }
                $model->spatieRoles()->attach($roles);
                $model->unsetRelation('roles');
            });
        }

        if (is_a($this, Permission::class)) {
            $this->forgetCachedPermissions();
        }

        return $this;
    }

    public function assignRole($roles, int $evento_id = null)
    {
        if ($evento_id == null) {
            return $this->spatieAssignRole($roles);
        }

        $roles = $this->collectRoles($roles);

        $model = $this->getModel();

        if ($model->exists) {
            $this->spatieRoles()->attach($roles, ['evento_id' => $evento_id]);
            $model->unsetRelation('roles');
        } else {
            $class = \get_class($model);

            $class::saved(function ($object) use ($roles, $model, $evento_id) {
                if ($model->getKey() != $object->getKey()) {
                    return;
                }
                $model->spatieRoles()->attach($roles, ['evento_id' => $evento_id]);
                $model->unsetRelation('roles');
            });
        }

        if (is_a($this, Permission::class)) {
            $this->forgetCachedPermissions();
        }

        return $this;
    }

    public function syncRoles($roles, $evento_id = null)
    {
        if ($evento_id === null) {
            return $this->spatieSyncRoles($roles);
        }

        $this->rolesEvento($evento_id)->detach();

        return $this->assignRole($roles, $evento_id);
    }

    public function spatieSyncRoles($roles)
    {
        $this->rolesWithNullEvento()->detach();

        return $this->assignRole($roles);
    }

    public function removeRole($role, int $evento_id = null)
    {
        if ($evento_id == null) {
            return $this->spatieRemoveRole($role);
        }

        if (!($role instanceof \App\Models\Role)) {
            $role = $this->getStoredRole($role);
        }

        $this->rolesEvento($evento_id)->detach($role);

        $this->unsetRelation('roles');

        if (is_a($this, Permission::class)) {
            $this->forgetCachedPermissions();
        }

        return $this;
    }

    public function removeMultipleRoles($roles, int $evento_id)
    {
        foreach ($roles as $role) {
            $this->removeRole($role, $evento_id);
        }
    }

    public function permissionsEvento(int $evento_id = null): array
    {
        $rolesEvento = $this->rolesEvento($evento_id)->get();
        $permissions = [];
        foreach ($rolesEvento as $role) {
            $permissions[$role->id] = $this->rolePermissions($role)->all();
        }
        return $permissions;
    }

    public function rolePermissions($role): BelongsToMany|\Illuminate\Support\Collection
    {
        if (!$role) {
            throw new Exception('Role not found');
        }

        $permissions = DB::table('role_has_permissions')
            ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
            ->where('role_has_permissions.role_id', $role->id)
            ->select('permissions.*')
            ->get();

        return $permissions;
    }

    public function hasPermissionTo($permission, int $evento_id = null, ?string $guard = null): bool
    {
        if (
            $evento_id === null ||
            $this->hasRole([RoleEnum::ADMIN->value])
        ) {
            return $this->spatieHasPermissionTo($permission, $guard);
        }

        if ($permission instanceof \BackedEnum) {
            $permission = $permission->value;
        }

        if (is_int($permission) || PermissionRegistrar::isUid($permission)) {
            $permission = $this->getPermissionClass()::findById($permission);
        }

        if (is_string($permission)) {
            $permission = $this->getPermissionClass()::findByName($permission);
        }

        if (!$permission instanceof Permission) {
            throw new PermissionDoesNotExist();
        }

        $hasPermission = false;

        $rolesEvento = $this->rolesEvento($evento_id)->get();

        foreach ($rolesEvento as $role) {
            if ($this->rolePermissions($role)->contains('id', $permission->id)) {
                $hasPermission = true;
                break;
            }
        }

        return $hasPermission;
    }
}
