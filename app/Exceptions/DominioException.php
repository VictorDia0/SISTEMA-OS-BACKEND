<?php

namespace App\Exceptions;

use Exception;

class DominioException extends Exception
{
    public function __construct(string $message, int $code, ?Exception $previous = null, private ?bool $error = true)
    {
        parent::__construct($message, $code, $previous);
    }

    public function isError(): bool
    {
        return $this->error;
    }
}
