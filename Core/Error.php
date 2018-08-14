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
     * Exception handler
     * @param \Exception $exception The exception
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return void

     */
    public static function exceptionHandler($exception): void
    {
        //code is 404 (not found) or 500 (general error)
        $code = $exception->getCode();
        if ($code != 404) {
            $code = 500;
        }
        $viewData[] = [];
        //always set the message to be sent
        $viewData['exceptionMessage'] = $exception->getMessage();

        http_response_code($code);

        //Constructing the error message to send to twig
        if (\App\Config::SHOW_ERRORS) {
            $viewData['showErrors'] = true; //sending the config option down to twig
            $viewData['classException'] = get_class($exception);
            $viewData['stackTrace'] = $exception->getTraceAsString();
            $viewData['thrownIn'] = $exception->getFile() . " On line " . $exception->getLine();
        }

        $view = new View();

        //Making sure that the twig template renders correctly.
        try{
            $view->renderTemplate('ErrorPages/'.$code . '.twig', $viewData);
        }catch (\Exception $e){
            echo 'Twig Error : '.htmlspecialchars($e->getMessage());
        }


    }
}