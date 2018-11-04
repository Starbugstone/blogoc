<?php

namespace Core;

use Throwable;

/**
 * Custom error handler for catchable errors. All others should throw an error page
 * this is intercepted in the Error class
 * Class BlogocException
 * @package Core
 */
class BlogocException extends \Exception
{

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}