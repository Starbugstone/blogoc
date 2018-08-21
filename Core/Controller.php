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
        'Csrf',
        'AlertBox'
    ];

    /**
     * We need to declare out modules here, else they will be public
     * @var object
     */
    protected $csrf;
    protected $alertBox;

    /**
     * Controller constructor.
     * @param Container $container
     *
     * @throws \ErrorException
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

        //removing any duplicates that could have been passed in child classes
        $this->loadModules = array_unique($this->loadModules);

        //We load all our module objects into our object
        foreach ($this->loadModules as $loadModule) {
            $loadModuleObj = 'Core\\Modules\\' . $loadModule;

            $loadModuleName = lcfirst($loadModule);
            $loadedModule = new $loadModuleObj($this->container);
            //Modules must be children of the Module template
            if (!is_subclass_of($loadedModule, 'Core\Modules\Module')) {
                throw new \ErrorException('Modules musit be a sub class of module');
            }

            //we are not allowed to create public modules, they must be a placeholder ready
            if (!property_exists($this, $loadModuleName)) {
                throw new \ErrorException('class var ' . $loadModuleName . ' not present');
            }
            $this->$loadModuleName = $loadedModule;
        }
        $this->session = $this->container->getSession();

        //Setting up csrf token security for all calls
        $this->data['csrf_token'] = $this->csrf->getCsrfKey(); //storing the security id into the data array to be sent to the view and added in the meta head
    }

    public function index()
    {
        //if no index, then redirect to the home page or throw an error if in dev; just for debugging purposes
        if (Config::DEV_ENVIRONMENT) {
            throw new \ErrorException("no index() available in controller call");
        }
        $this->container->getResponse()->redirect();
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
        //checking if any alerts and pas the to the template
        if ($this->alertBox->alertsPending()) {
            $this->data['alert_messages'] = $this->alertBox->getAlerts();
        }
        $twig = $this->container->getTemplate();
        $twig->display($template . '.twig', $this->data);
    }
}