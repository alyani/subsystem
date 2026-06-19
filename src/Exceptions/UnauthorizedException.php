<?php

namespace Alyani\Subsystem\Exceptions;

use Exception;

class UnauthorizedException extends Exception
{
    /**
     * Create a new Unauthorized exception.
     *
     * @param string $message
     * @param int $code
     * @return void
     */
    public function __construct($message = '', $code = 403)
    {
        parent::__construct($message, $code);
    }
}
