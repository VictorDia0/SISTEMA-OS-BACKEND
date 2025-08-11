<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class UserNotFoundException extends DominioException
{
    public function __construct(
        string $message = 'Usuário não encontrado.',
        int $code = Response::HTTP_NOT_FOUND,
        ?\Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
