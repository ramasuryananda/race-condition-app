<?php

namespace App\Exceptions;

use Exception;

class InvalidBalanceException extends Exception
{
    public function __construct($message = 'Balance is not enough to do transaction', $code = 400, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
