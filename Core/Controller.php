<?php

namespace Core;

/**
 * Class Controller
 * @package Core
 */
abstract class Controller
{
    /**
     * the data that will be pushed to the view
     * @var array
     */
    protected $data = [];

    /**
     * this will hold the view object
     * @var view object
     */
    //protected $view;

    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getView($name, $args = []) {
        $twig = $this->container->getTemplate();
        return $twig->render($name.'.twig', $args);
    }

    public function renderView($template, $args = []): void
    {
        $twig = $this->container->getTemplate();
        echo $twig->render($template, $args);
    }

}