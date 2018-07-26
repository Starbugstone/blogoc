<?php

namespace Core;
/*
 * Router, will take in url parameters in the form
 * index.php?url=Controller/Method/param1/param2
 *
 * if we have a special section (admin section), load that namespace
 *
 * PHP version 7
 */
class Router{

    /**
     * the default namespace
     * @var string
     */
    private $currentNamespace = 'App\Controllers\\';

    /**
     * the default controller
     * @var string
     */
    private $currentController = 'Home';

    /**
     * the default method
     * @var string
     */
    private $currentMethod = 'index';

    /**
     * the current paramters
     * @var string
     */
    private $currentParams = [];

    /**
     * The special sections that have there own namespace.
     * All in lower, will be converted to camelCase later
     * @var array
     */
    private $sections = [
        'admin'
    ];


    /**
     * Router constructor, will set the controler, method and params
     *
     * will then call the dispatcher to instantiate the controller and method
     *
     */
    public function __construct(){
        //get the current url
        $url = $this->getUrl();

        //checking if a special namespace is present at the start of the url.
        //if so, then strip and set the new namespace
        if(isset($url[0]) && in_array($url[0],$this->sections) ){
            $specialNamespace = array_shift($url);

            //making sure we have a single backslash
            $specialNamespace = rtrim($specialNamespace,'\\').'\\';

            //capitalize the special namespace
            $specialNamespace = $this->convertToStudlyCaps($specialNamespace);

            $this->currentNamespace .= $specialNamespace;
        }

        //applying the controlers and methods
        if (isset($url[0]) && $url[0] != null){
            $this->currentController = $this->convertToStudlyCaps($url[0]);
            unset($url[0]);
        }

        if(isset($url[1]) && $url[1] != null){
            $this->currentMethod = $this->convertToCamelCase($url[1]);
            unset($url[1]);
        }

        //grabbing the remaining parameters
        $this->currentParams = $url? array_values($url) : [];

        echo 'in namespace '.$this->currentNamespace.'<br>';
        echo 'Controller to call '.$this->currentController.'<br>';
        echo 'method to call '.$this->currentMethod.'()<br>';
        var_dump($url);
        echo '<br><hr>';
        var_dump($this->currentParams);

        //TODO Check if method exists and call
        $this->dispatch();
    }


    /**
     * Get the controller, action and params from the url= string
     *
     * @return array decomposed url
     */
    protected function getUrl(): array{
        if(isset($_GET['url'])){ //$url = $_GET['url'] ?? ''  en php 7.1
            //remove right slash
            $url = rtrim($_GET['url'], '/');

            //convert all to lower. Will convert to camelCase after
            $url = strtolower($url);

            //sanitize the url for security
            $url = filter_var($url, FILTER_SANITIZE_URL);

            $url = explode('/', $url);

            return $url;
        }
        return [];
   }


    /**
     * Run the router call and instantiate the controoler + method
     *
     * @return void
     */
    protected function dispatch(){

   }

    /**
     * Convert the string with hyphens to StudlyCaps,
     * e.g. post-authors => PostAuthors
     *
     * @param string $string The string to convert
     *
     * @return string
     */
    protected function convertToStudlyCaps($string): string{
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

    /**
     * Convert the string with hyphens to camelCase,
     * e.g. add-new => addNew
     *
     * @param string $string The string to convert
     *
     * @return string
     */
    protected function convertToCamelCase($string): string{
        return lcfirst($this->convertToStudlyCaps($string));
    }
}