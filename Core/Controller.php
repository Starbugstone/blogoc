<?php

namespace Core;

use Twig\Template;

/**
 * Class Controller
 * @package Core
 *
 * PHP version 7
 */
abstract class Controller
{
    /**
     * the data that will be pushed to the view
     * @var array
     */
    protected $data = [];

    /**
     * @var Container dependency injector
     */
    protected $container;

    /**
     * @var Dependency\Session the session handler
     */
    protected $session;

    /**
     * this will automaticly load all the modules listed and store them as $moduleName in tle class
     * Child classes can call aditional modules by calling $this->
     * @var array List of modules to load
     */
    protected $loadModules = [
        'Csrf'
    ];

    /**
     * Controller constructor.
     * @param Container $container
     *
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

        //We load all our module objects into our object
        foreach ($this->loadModules as $loadModule){
            $loadModuleObj = 'Core\\Modules\\'.$loadModule;
            $loadModuleName = strtolower($loadModule);
            $this->$loadModuleName = new $loadModuleObj($this->container);
        }
        $this->session = $this->container->getSession();

        //Setting up csrf token security for all calls
        $this->data['csrf_token'] = $this->csrf->getCsrfKey(); //storing the security id into the data array to be sent to the view and added in the meta head
    }

    /**
     * Calls the templating engine and returns the rendered view
     *
     * @param $template string the template file name. .twig will be appended
     * @return string the rendered template
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function getView($template)
    {
        $twig = $this->container->getTemplate();
        return $twig->render($template . '.twig', $this->data);
    }

    /**
     * rendering the view
     *
     * @param $template string the template file
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function renderView($template): void
    {
        $twig = $this->container->getTemplate();
        $twig->display($template . '.twig', $this->data);
    }
}