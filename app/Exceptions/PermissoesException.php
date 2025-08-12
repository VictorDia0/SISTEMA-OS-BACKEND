<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class PermissoesException extends DominioException
{
    public function __construct(
        $message = 'Erro interno ao atribuir permissão ao usuário!',
        $code = Response::HTTP_INTERNAL_SERVER_ERROR,
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
