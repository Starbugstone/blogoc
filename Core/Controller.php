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
    protected $view;

    public function __construct()
    {
        $this->view = new \Core\View();
    }

}