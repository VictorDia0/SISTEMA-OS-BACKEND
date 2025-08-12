<?php

namespace App\Models;

use App\Enums\AccountStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PlanEnum;
use App\Models\Traits\HasRolesWithContext;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasRolesWithContext;

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(
            fn($user) => empty($user->{$user->getKeyName()})
                ? ($user->{$user->getKeyName()} = Str::uuid()->toString())
                : null
        );
    }

    protected $fillable = [
        'id',
        'name',
        'surname',
        'phone_number',
        'email',
        'password',
        'plan',
        'account_status',
        'payment_status',
        'email_verified_at',
        'verification_code',
        'is_verified',
        'is_active',
        'plan_start_date',
        'plan_end_date',
    ];

    protected $hidden = ['password', 'remember_token', 'verification_code'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
            'plan' => PlanEnum::class,
            'account_status' => AccountStatusEnum::class,
            'payment_status' => PaymentStatusEnum::class,
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function permissions()
    {
        return $this->hasManyThrough(Permission::class, Role::class, 'id', 'id', 'id', 'id')
            ->withPivot('role_id', 'permission_id');
    }
    public function hasRole($roleKey)
    {
        return $this->roles->contains('key', $roleKey);
    }

    public function hasPermission($permissionName)
    {
        return $this->permissions()->contains('name', $permissionName);
    }


    public static function getUserByIdOrFail(string $id): User
    {
        return static::findOrFail($id);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function markEmailVerified(): bool
    {
        $this->email_verified_at = now();

        return $this->save();
    }

    public function hasVerifiedEmail(): bool
    {
        return (bool) !is_null($this->email_verified_at);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    public static function marcarEmailComoVerificado(string $email): User
    {
        $user = self::getUsuarioPorEmail($email);
        $user->markEmailAsVerified();
        return $user;
    }
    public static function getUsuarioPorEmail(string $email): ?User
    {
        return self::where('email', $email)->first();
    }

    public function generateVerificationCode(int $length = 6): string
    {
        $code = str_pad((string) rand(0, 10 ** $length - 1), $length, '0', STR_PAD_LEFT);
        $this->verification_code = $code;
        $this->save();

        return $code;
    }
}
