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
     * We get the module list and construct it. We can also over wright the module in app but sill has to inherit from core module
     * @param Container $container
     *
     * @throws \ErrorException on module loading error
     * @throws \ReflectionException should never be thrown since only child classes call this
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

        //removing any duplicates that could have been passed in child classes
        $this->loadModules = array_unique($this->loadModules);

        //We load all our module objects into our object
        foreach ($this->loadModules as $loadModule) {
            $this->loadModule($loadModule);
        }
        $this->session = $this->container->getSession();

        //Setting up csrf token security for all calls
        $this->data['csrf_token'] = $this->csrf->getCsrfKey(); //storing the security id into the data array to be sent to the view and added in the meta head
    }

    /**
     * load the module to the object
     * We look for module in the namespace, then in app and finally in the core.
     * This enables us to over-ride the core or app module with a custom module for the namespace.
     * @param $loadModule string the module to search for and load
     * @throws \ErrorException if module doesn't exits
     * @throws \ReflectionException
     */
    private function loadModule($loadModule)
    {
        $loadModuleName = lcfirst($loadModule);
        $loadModuleObj = $this->getModuleNamespace($loadModule);
        //Modules must be children of the Module template
        if (!is_subclass_of($loadModuleObj, 'Core\Modules\Module')) {
            throw new \ErrorException('Module ' . $loadModuleName . ' must be a sub class of module');
        }
        $loadedModule = new $loadModuleObj($this->container);
        //we are not allowed to create public modules, they must be a placeholder ready
        if (!property_exists($this, $loadModuleName)) {
            throw new \ErrorException('the protected or private variable of ' . $loadModuleName . ' is not present');
        }
        $this->$loadModuleName = $loadedModule;
    }

    /**
     * takes a module to load and verifies if exists in the current namespace modules, app modules or core modules
     * @param $loadModule string Module to look for
     * @return string the full module namespace
     * @throws \ErrorException if no module is found
     * @throws \ReflectionException Should never happen since we are calling on $this
     */
    private function getModuleNamespace($loadModule)
    {
        $childClass = new \ReflectionClass(get_class($this));
        $childClassNamespace = $childClass->getNamespaceName();
        //check in classNameSpace
        if (class_exists($childClassNamespace . '\\Modules\\' . $loadModule)) {
            $this->addToDevHelper('module ' . $loadModule . ' loaded', $childClassNamespace . '\\' . $loadModule);
            return $childClassNamespace . '\\' . $loadModule;
        }
        //check in app
        if (class_exists('App\\Modules\\' . $loadModule)) {
            $this->addToDevHelper('module ' . $loadModule . ' loaded', 'App\\Modules\\' . $loadModule);
            return 'App\\Modules\\' . $loadModule;
        }
        //check in core, send error popup if overcharged
        if (class_exists('Core\\Modules\\' . $loadModule)) {
            $this->addToDevHelper('module ' . $loadModule . ' loaded', 'Core\\Modules\\' . $loadModule);
            return 'Core\\Modules\\' . $loadModule;
        }

        //if we are here then no module found
        throw new \ErrorException('module ' . $loadModule . ' does not exist or not loaded');

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
     * @throws \ReflectionException
     */
    public function renderView($template): void
    {
        //checking if any alerts and pas the to the template
        if ($this->alertBox->alertsPending()) {
            $this->data['alert_messages'] = $this->alertBox->getAlerts();
        }
        if (Config::DEV_ENVIRONMENT) {
            $this->devHelper();
        }
        $twig = $this->container->getTemplate();
        $twig->display($template . '.twig', $this->data);
    }

    /**
     * construct a dev helper panel
     * @throws \ReflectionException
     */
    protected function devHelper()
    {
        $this->data['dev'] = true;

        $this->addToDevHelper('Class Methods', get_class_methods(get_class($this)));
        $this->addToDevHelper('Session Vars', $this->session->getAllSessionVars());
        $this->addToDevHelper('uri', $this->container->getRequest()->getUri());
        $childClassNamespace = new \ReflectionClass(get_class($this));
        $childClassNamespace = $childClassNamespace->getNamespaceName();
        $this->addToDevHelper('Child Namespace', $childClassNamespace);

        //for our object variables, we don't want the devinfo
        $objVars = get_object_vars($this);
        unset($objVars['data']['dev_info']);
        $this->addToDevHelper('Object Variables', $objVars);
    }

    /**
     * add info to our dev helper panel
     * @param $name
     * @param $var
     */
    protected function addToDevHelper($name, $var)
    {
        //only populate if in dev environment
        if (Config::DEV_ENVIRONMENT){
            $classMethods = [];
            $classMethods[$name] = $var;
            if (!isset($this->data['dev_info'])) {
                $this->data['dev_info'] = [];
            }
            $this->data['dev_info'] += $classMethods;
        }
    }
}