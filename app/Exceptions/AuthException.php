<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class AuthException extends DominioException
{
    public function __construct(
        $message = 'Erro interno ao autenticar usuário!',
        $code = Response::HTTP_INTERNAL_SERVER_ERROR,
        $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
