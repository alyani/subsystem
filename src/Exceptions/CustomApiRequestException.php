<?php

namespace Alyani\Subsystem\Exceptions;

use Exception;

class CustomApiRequestException extends Exception
{
    /**
     * Create a new customApiRequest exception.
     *
     * @param string $message
     * @param array $guards
     * @return void
     */
    public function __construct($message = '', $code = 200)
    {
        parent::__construct($message, $code);
    }
}
