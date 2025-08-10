<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class VerificacaoEmail extends Model
{
    public function __construct(string $email = null, string $token = null, $expired_at = null)
    {
        if($email !== null) {
            $this->email = $email;
        }

        $this->token = $token ?? Str::uuid();
        $this->expired_at = $expired_at ?? Carbon::now()->addMinutes(10);
    }

    public static function getVerificacaoPorEmail(string $email): Builder|null
    {
        return self::where('email', $email);
    }

    public static function verificacao(string $email, string $token): VerificacaoEmail|null
    {
        return self::where(['email' => $email, 'token' => $token])->first();
    }
}
