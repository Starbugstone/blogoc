<?php

namespace Core;

/*
 * error and exception handler
 */
class Error{
    /*
     * Error handler. Convert all errors to exceptions by throwing an errorException
     *
     * @param int $level  Error level
     * @param string $message  Error message
     * @param string $file  Filename the error was raised in
     * @param int $line  Line number in the file
     *
     * @return void
     *
     */
    public static function errorHandler($level, $message, $file, $line){
        if(error_reporting() !== 0){
            //to keep the @ operator working
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

    /*
     * Exception handler
     * @param Exception $exception  The exception
     *
     * @return void
     */
    //TODO add option to put the stack trace in a log file or just have prettier error pages
    public static function exceptionHandler($exception){
        //code is 404 (not found) or 500 (general error)
        $code = $exception->getCode();
        if ($code != 404){
            $code = 500;
        }

        http_response_code($code);
        if(\App\Config::SHOW_ERRORS){
            echo "<h1>Fatal error</h1>";
            echo "<p>Uncaught exception : '".get_class($exception)."'</p>";
            echo "<p>Message : '".$exception->getMessage()."'</p>";
            echo "<p>Stack trace : <pre>".$exception->getTraceAsString()."</pre></p>";
            echo "<p>thrown in '".$exception->getFile()." On line ".$exception->getLine()."</p>";
        }else{
            $data['exceptionMessage'] = $exception->getMessage();
            View::renderTemplate($code.'.twig', $data);
        }

    }
}