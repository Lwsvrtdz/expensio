<?php

namespace App\Exceptions;

use Exception;

class CustomException extends Exception
{
    public static function internalException(): self
    {
        return new self('Internal exception', 500);
    }

    public static function noUserFound(): self
    {
        return new self('No user found', 404);
    }
}
