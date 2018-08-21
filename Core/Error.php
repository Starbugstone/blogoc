<?php

namespace Core;

/**
 * error and exception handler
 * PHP version 7
 */
class Error
{
    /**
     * Error handler. Convert all errors to exceptions by throwing an errorException
     *
     * @param int $level Error level
     * @param string $message Error message
     * @param string $file Filename the error was raised in
     * @param int $line Line number in the file
     *
     * @return void
     *
     * @throws \ErrorException to transform all errors into exceptions if the error reporting php configuration is set
     *
     */
    public static function errorHandler($level, $message, $file, $line): void
    {
        if (error_reporting() !== 0) {
            //to keep the @ operator working
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Exception handler. Will show a custom error page when the exception is called.
     * Also checks if json error and treats it as json response
     * @param \Exception $exception The exception
     *
     * @return void
     */
    public static function exceptionHandler($exception): void
    {
        $code = $exception->getCode();

        //If we have a json exception thrown, we return a json error and stop
        if (get_class($exception) === 'Core\JsonException') {
            $code = 400; //sending back a bad request error
            if (Config::DEV_ENVIRONMENT) { //If we are in dev and using ajax, we want to see the error for debugging
                $code = 200;
            }
            http_response_code($code);
            header('Content-Type: application/json');
            die(json_encode(['error' => $exception->getMessage()]));
        }

        //code is 404 (not found) or 500 (general error)
        if ($code != 404) {
            $code = 500;
        }
        $viewData = [];
        //always set the message to be sent
        $viewData['exceptionMessage'] = $exception->getMessage();

        http_response_code($code);

        //Constructing the error message to send to twig
        if (Config::DEV_ENVIRONMENT) {
            $viewData['showErrors'] = true; //sending the config option down to twig
            $viewData['classException'] = get_class($exception);
            $viewData['stackTrace'] = $exception->getTraceAsString();
            $viewData['thrownIn'] = $exception->getFile()." On line ".$exception->getLine();
        }

        $container = new Container();

        //Making sure that the twig template renders correctly.
        try {
            $twig = $container->getTemplate();
            $twig->display('ErrorPages/'.$code.'.twig', $viewData);
        } catch (\Exception $e) {
            echo 'Twig Error : '.$e->getMessage();
        }


    }
}