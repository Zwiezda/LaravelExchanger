<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

abstract class JsonAPIException extends Exception
{
    public abstract function render() : Response;
}
