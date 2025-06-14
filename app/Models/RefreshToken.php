<?php

namespace App\Models;

use App\Exceptions\AuthException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Response;

class RefreshToken extends Model
{
    use HasFactory;

    protected $table = 'refresh_tokens';
    protected $fillable = ['user_id', 'token', 'device', 'expires_at', 'ip_address', 'user_agent'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTTL(): int
    {
        $expiresAt = $this->expires_at;
        $ttl = $expiresAt->diffInMinutes(Carbon::now());

        return max($ttl, 0);
    }

    public static function make(User $user, string $device, int $ttl = null): array
    {
        $ttl = $ttl ?? config('jwt.refresh_ttl', env('JWT_REFRESH_TTL'));

        self::where('user_id', $user->id)->where('device', $device)->delete();

        $exp = Carbon::now()->addMinutes($ttl);
        $tokenPlain = self::gerarToken();

        $model = self::create([
            'user_id' => $user->id,
            'token' => hash('sha256', $tokenPlain),
            'device' => $device,
            'expires_at' => $exp,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return [
            'model' => $model,
            'token' => $tokenPlain,
        ];
    }

    public static function validarToken(string $token, string $device): RefreshToken
    {
        $hashedToken = hash('sha256', $token);
        $refreshToken = RefreshToken::where('token', $hashedToken)->first();

        if (!$refreshToken) {
            throw new AuthException('O token informado é inválido!', Response::HTTP_UNAUTHORIZED);
        }

        if ($refreshToken->device !== $device) {
            $refreshToken->delete();

            throw new AuthException('O token informado não corresponde ao dispositivo!', Response::HTTP_UNAUTHORIZED);
        }

        if ($refreshToken->expires_at < now()) {
            $refreshToken->delete();

            throw new AuthException('O token informado já expirou!', Response::HTTP_UNAUTHORIZED);
        }

        $refreshToken->update([
            'last_used_at' => now(),
            'ip_address' => request()->ip(),
        ]);

        return $refreshToken;
    }

    public static function logout(User $user): void
    {
        self::where('user_id', $user->id)->delete();
    }

    public static function gerarToken(): string
    {
        return bin2hex(random_bytes(64));
    }

    public static function limparExpirados(): void
    {
        self::where('expires_at', '<', now())->delete();
    }
}
