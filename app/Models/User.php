<?php

namespace App\Models;

use App\Enums\AccountStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PlanEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens;

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    use HasFactory, Notifiable;

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
        'verification_code',
        'is_verified',
        'is_active',
        'plan_start_date',
        'plan_end_date',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'plan' => PlanEnum::class,
            'account_status' => AccountStatusEnum::class,
            'payment_status' => PaymentStatusEnum::class,
        ];
    }


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public static function getUserByEmail(string $email): User|null
    {
        return self::where('email', $email)->first();
    }
}
