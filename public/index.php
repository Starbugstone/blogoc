<?php
//Calling the composer autoloader. It has our namespaces in PSR-4
require_once '../vendor/autoload.php';

use Core\Container;
use Core\Router;

error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

$container = new Container();

$router = new Router($container);