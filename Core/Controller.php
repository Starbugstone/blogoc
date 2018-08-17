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
     * @var session the session handler
     */
    protected $session;

    /**
     * Controller constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->session = $this->container->getSession();
        $this->session->setCsfr(); //setting our unique security id, this will only update if it isn't already present in the $_Session
        $this->data['csrf_token'] = $this->session->getCsrf(); //storing the security id into the data array to be sent to the view and added in the meta head
    }

    /**
     * Calls the templating engine and returns the rendered view
     *
     * @param $template string the template file name. .twig will be appended
     * @param array $args the data to pass to the view
     * @return string the rendered template
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function getView($template, $args = []) {
        $twig = $this->container->getTemplate();
        return $twig->render($template.'.twig', $args);
    }

    /**
     * rendering the view
     *
     * @param $template string the template file
     * @param array $args the data to pass to the view
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function renderView($template, $args = []): void
    {
        $twig = $this->container->getTemplate();
        $twig->display($template.'.twig', $args);
    }

    /**
     * gets our depandancy injection to be passed to models
     * @return Container
     */
    public function getContainer(){
        return $this->container;
    }

}