<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class BadRatingServiceResponseException extends JsonAPIException
{
    public function __construct($message = '')
    {
        parent::__construct($message, 500);
    }

    public function render() : Response
    {
        return response()->json([
            'Message' => $this->message,
            'ErrorCode' => $this->code
        ], $this->code);
    }
}
