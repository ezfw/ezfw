<?php

namespace EZFW\Http;

use Exception;

class ErrorHandler
{
    public function handle(Exception $e)
    {
        return (new Response)->internalServerError('500: Internal Server Error');
    }
}
