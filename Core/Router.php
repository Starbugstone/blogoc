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
     * The special sections that have there own namespace.
     * All in lower, will be converted to camelCase later
     * @var array
     */
    private $sections = [
        'admin'
    ];

    /**
     * The default namespace
     * @var string
     */
    private $defaultNamespace = 'App\Controllers\\';



    /**
     * Router constructor, will get the current url
     */
    public function __construct(){
        //get the current url
        $url = $this->getUrl();

        //checking if a special namespace is present at the start of the url. if so, then strip and pass
        $special = '';
        if(in_array($url[0],$this->sections) ){
            $special = array_shift($url);
        }
        $namespace = $this->getNamespace($special);

        //applying the controlers and methods
        if ($url[0] != null){
            $this->currentController = $this->convertToStudlyCaps($url[0]);
            unset($url[0]);
        }

        if(isset($url[1]) && $url[1] != null){
            $this->currentMethod = $this->convertToCamelCase($url[1]);
            unset($url[1]);
        }
        echo 'in namespace '.$namespace.'<br>';
        echo 'Controller to call '.$this->currentController.'<br>';
        echo 'method to call '.$this->currentMethod.'()<br>';
        //TODO take care of the paramters

    }


    /**
     * Get the controller, action and params from the url= string
     *
     * @return array decomposed url
     */
    //TODO : see if there isn't a better way that a brutal $_GET
   public function getUrl(){
        if(isset($_GET['url'])){
            //remove right slash
            $url = rtrim($_GET['url'], '/');

            //convert all to lower. Will convert to camelCase after
            $url = strtolower($url);

            //sanitize the url for security
            $url = filter_var($url, FILTER_SANITIZE_URL);

            $url = explode('/', $url);

            return $url;
        }
   }

   /**
    * returns the namespace
    *
    * @param string $special special namespace
    * e.g. admin => $namespace\Admin\
    *
    * @return string the required namespace
    */
   public function getNamespace($special = ''){
       if($special !== ''){
           //making sure we have a single backslash
           $special = rtrim($special,'\\').'\\';

           //capitalize the special namespace
           $special = $this->convertToStudlyCaps($special);
       }


       $namespace = $this->defaultNamespace.$special;

       return $namespace;
   }

    /**
     * Convert the string with hyphens to StudlyCaps,
     * e.g. post-authors => PostAuthors
     *
     * @param string $string The string to convert
     *
     * @return string
     */
    protected function convertToStudlyCaps($string){
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
    protected function convertToCamelCase($string){
        return lcfirst($this->convertToStudlyCaps($string));
    }
}