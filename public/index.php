<?php
//Calling the composer autoloader. It has our namespaces in PSR-4
require_once '../vendor/autoload.php';

echo 'in public folder<br>';
echo $_GET['url'];