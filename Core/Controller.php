<?php

namespace Core;

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
     * Our Csrf security module for ajax calls
     * @var Csrf
     */
    protected $Csrf;

    /**
     * Controller constructor.
     * @param Container $container
     *
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->session = $this->container->getSession();

        //Setting up csrf token security for all calls
        $this->Csrf = new Csrf($container);
        $this->data['csrf_token'] = $this->Csrf->getCsrf(); //storing the security id into the data array to be sent to the view and added in the meta head
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

    /**
     * gets our depandancy injection to be passed to models
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * gets out csrf object
     * @return Csrf
     */
    public function getCsrf()
    {
        return $this->Csrf;
    }

}