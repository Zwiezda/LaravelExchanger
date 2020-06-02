<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class BadRequestFormatException extends JsonAPIException
{
    private $fieldErrors;

    public function __construct(array $fieldErrors)
    {
        parent::__construct('', 400);

        $this->fieldErrors = $fieldErrors;
    }

    public function render() : Response
    {
        return response()->json([
            'Message' => 'Malformed Request!',
            'FieldMessages' => $this->fieldErrors,
            'ErrorCode' => $this->code
        ], $this->code);
    }
}
