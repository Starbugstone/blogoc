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
     * We loog for module in the namespace, then in app and finaly in the core.
     * This enables us to over-ride the core or app module with a custom module for the namespace.
     * @param $loadModule string grabbed from the loadmodules array
     * @throws \ErrorException if module doesn't exits
     * @throws \ReflectionException should never be thrown since only child classes call this
     */
    private function loadModule($loadModule)
    {
        $loadModuleName = lcfirst($loadModule);
        //checking for module in namespace, app and core.
        $found = false;
        $childClassNamespace = new \ReflectionClass(get_class($this));
        $childClassNamespace = $childClassNamespace->getNamespaceName();
        //check in classNameSpace, make sure we are not already in App or core
        if (class_exists($childClassNamespace . '\\Modules\\' . $loadModule)) {
            if($childClassNamespace !== 'App\\Modules\\' || $childClassNamespace !== 'Core\\Modules\\'){
                $loadModuleObj = $childClassNamespace . '\\' . $loadModule;
                $found = true;
            }
        }
        //check in app
        if (class_exists('App\\Modules\\' . $loadModule)) {
            if($found && Config::DEV_ENVIRONMENT){
                $this->alertBox->setAlert($loadModule.' already defined in namespace', 'error');
            }
            if(!$found){
                $loadModuleObj = 'App\\Modules\\' . $loadModule;
                $found = true;
            }
        }
        //check in core, send error popup if overcharged
        if (class_exists('Core\\Modules\\' . $loadModule)) {
            if($found && Config::DEV_ENVIRONMENT){
                $this->alertBox->setAlert($loadModule.' already defined in app', 'error');
            }
            if(!$found){
                $loadModuleObj = 'Core\\Modules\\' . $loadModule;
                $found = true;
            }
        }
        if(!$found) {
            throw new \ErrorException('module ' . $loadModule . ' does not exist');
        }

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
        if (Config::DEV_ENVIRONMENT) {
            $this->devHelper();
        }
        $twig = $this->container->getTemplate();
        $twig->display($template . '.twig', $this->data);
    }

    protected function devHelper($var = '')
    {
        if (!isset($this->data['dev_info'])) {
            $this->data['dev'] = true;
            $classMethods = [];
            if ($var != '') {
                $classMethods['passed_var'] = $var;
            }
            $classMethods['class_object_methods'] = get_class_methods(get_class($this));
            $classMethods['class_object_vars'] = get_object_vars($this);
            $classMethods['session_vars'] = $_SESSION;
            $classMethods['namespace'] = __NAMESPACE__;
            $childClassNamespace = new \ReflectionClass(get_class($this));
            $childClassNamespace = $childClassNamespace->getNamespaceName();
            $classMethods['classNamespace'] = $childClassNamespace;

            $this->data['dev_info'] = $classMethods;
        }

    }
}