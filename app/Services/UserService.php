<?php

namespace App\Services;

use App\Exceptions\EmailVerificationException;
use App\Exceptions\UserNotFoundException;
use App\Models\User;
use App\Notifications\RedefinirSenhaEmailNotification;
use App\Strategies\Emails\IVerificacaoEmailStrategy;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class UserService implements IUserService
{
    public function __construct(protected IVerificacaoEmailService $emailService,
    protected IVerificacaoEmailStrategy $emailVerificationStrategy) {}

    public function getAllUsers(): Collection
    {
        return User::all();
    }

    public function getUserById(string $id): User
    {
        $user = User::getUserByIdOrFail($id);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    public function getUserByEmail(string $email): User
    {
        $user = User::getUserByEmail($email);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    public function enviarEmailRedefinirSenha(object $data): void
    {
        try {
            DB::beginTransaction();

            $usuario = User::getUsuarioPorEmail($data->email);
            if (is_null($usuario)) {
                throw new EmailVerificationException(
                    'O e-mail informado não está cadastrado na base de dados!',
                    Response::HTTP_NOT_FOUND
                );
            }

            $verificacao = $this->emailService->gerarVerificacao($data->email, Carbon::now()->addHours(24));
            $verificacao->save();

            $url = $this->emailVerificationStrategy->definirUrlRedefinirSenha($verificacao->token, $verificacao->email);
            Notification::send($usuario, new RedefinirSenhaEmailNotification($url));

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new EmailVerificationException('Erro interno ao tentar enviar email para redefinição de senha!');
        }
    }
}
