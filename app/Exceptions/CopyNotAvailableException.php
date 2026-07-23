<?php

namespace App\Exceptions;

use Exception;

class CopyNotAvailableException extends Exception
{
    protected $code = 409; // Conflict — semánticamente correcto para la API
}
