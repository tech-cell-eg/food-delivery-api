<?php

namespace App\Exceptions;

use Exception;

class MissingTokenException extends Exception
{
    //
    public function __construct($message = "Token is missing")
    {
        parent::__construct($message, 401);
    }
}
