<?php

namespace Core;
/**
 * Router, will take in url parameters in the form
 * index.php?url=Controller/Method/param1/param2
 *
 * if we have a special section (admin section), load that namespace
 *
 * PHP version 7
 */
class Router
{

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
     * the current parameters
     * @var string
     */
    private $currentParams = [];

    /**
     * The special sections that have there own namespace.
     * All in lower, will be converted to camelCase later
     * @var array
     */
    private $sections = [
        'admin',
        'ajax'
    ];

    private $container;


    /**
     * Router constructor, will set the controller, method and params
     *
     * will then call the dispatcher to instantiate the controller and method
     *
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        //get the current url
        $url = $this->getUrl();

        //checking if a special namespace is present at the start of the url.
        //if so, then strip and set the new namespace
        if (isset($url[0]) && in_array($url[0], $this->sections)) {
            $specialNamespace = array_shift($url);

            //making sure we have a single backslash
            $specialNamespace = rtrim($specialNamespace, '\\') . '\\';

            //capitalize the special namespace
            $specialNamespace = $this->convertToStudlyCaps($specialNamespace);

            $this->currentNamespace .= $specialNamespace;
        }

        //applying the controllers and methods
        if (isset($url[0]) && $url[0] != null) {
            $this->currentController = $this->convertToStudlyCaps($url[0]);
            unset($url[0]);
        }

        if (isset($url[1]) && $url[1] != null) {
            $this->currentMethod = $this->convertToCamelCase($url[1]);
            unset($url[1]);
        }

        //grabbing the remaining parameters
        $this->currentParams = $url ? array_values($url) : [];
        $this->dispatch();
    }


    /**
     * Get the controller, action and params from the url= string
     *
     * @return array decomposed url
     */
    protected function getUrl(): array
    {

        $url = $this->container->getRequest()->getData('url');
        if($url){
            //remove right slash
            $url = rtrim($url, '/');

            //convert all to lower for easier comparing. Will convert to camelCase after
            //this will avoid cap probs with links and user input
            $url = strtolower($url);

            //sanitize the url for security
            $url = filter_var($url, FILTER_SANITIZE_URL);

            //EXPLODE, BOOM, TRANSFORMERS, MICHAEL BAY
            $url = explode('/', $url);

            return $url;
        }
        return [];
    }


    /**
     * Run the router call and instantiate the controller + method
     * also takes care of throwing errors
     *
     * @return void
     *
     * @throws  \exception if the controller or method doesn't exist
     */
    protected function dispatch(): void
    {

        //try to create the controller object
        $fullControllerName = $this->currentNamespace . $this->currentController;

        //make sure the class exists before continuing
        if (!class_exists($fullControllerName)) {
            throw new \Exception("Class $fullControllerName doesn't exist", 404);
        }

        //instantiate our controller
        $controllerObj = new $fullControllerName($this->container);
        //try to run the associated method and the pass parameters
        $methodToRun = $this->currentMethod;

        //make sure our method exists before continuing
        if (!method_exists($controllerObj, $methodToRun)) {
            throw new \Exception("ERROR - Method $methodToRun() doesn't exist or is inaccessible");
        }

        call_user_func_array([$controllerObj, $methodToRun], $this->currentParams);
    }

    /**
     * Convert the string with hyphens to StudlyCaps,
     * e.g. post-authors => PostAuthors
     *
     * @param string $string The string to convert
     *
     * @return string
     */
    protected function convertToStudlyCaps($string): string
    {
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
    protected function convertToCamelCase($string): string
    {
        return lcfirst($this->convertToStudlyCaps($string));
    }
}