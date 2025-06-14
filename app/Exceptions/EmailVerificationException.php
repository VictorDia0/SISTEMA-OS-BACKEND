<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class EmailVerificationException extends DominioException
{
    public function __construct(
        $message = 'Erro interno ao verificar email!',
        $code = Response::HTTP_INTERNAL_SERVER_ERROR,
        $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
