<?php
namespace Core;
use Throwable;

/**
 * Custom error handeler for ajax / json errors.
 * this is intercepted in the Error class
 * When this is called, we return a json error with a code of 400 if in prod mode or we return the error in json format if in dev mode
 * Class JsonException
 * @package Core
 */
class JsonException extends \Exception {

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}