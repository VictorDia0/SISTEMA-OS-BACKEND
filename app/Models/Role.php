<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\RoleEnum;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'guard_name',
        'description'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public static function findByEnum(RoleEnum $roleEnum): ?Role
    {
        return static::where('key', $roleEnum->value)->first();
    }

    public function hasPermission(string $permissionName): bool
    {
        return $this->permissions()->where('name', $permissionName)->exists();
    }

    public function assignPermission(string $permissionName): void
    {
        $permission = Permission::firstOrCreate(['name' => $permissionName]);
        $this->permissions()->syncWithoutDetaching($permission);
    }

    public static function createDefaultRoles(): void
    {
        foreach (RoleEnum::cases() as $roleCase) {
            self::firstOrCreate(
                ['key' => $roleCase->value],
                ['name' => ucfirst($roleCase->value)]
            );
        }
    }
}
