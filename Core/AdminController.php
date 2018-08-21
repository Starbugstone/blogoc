<?php

namespace Core;

/**
 * The parent controller for all admin section controllers
 * Class AdminController
 * @package Core
 */
abstract class AdminController extends Controller
{
    /**
     * Out placeholders for modules
     * @var object
     */
    protected $auth;
    protected $alertBox;

    public function __construct(Container $container)
    {
        $this->loadModules[] = 'Auth';
        $this->loadModules[] = 'AlertBox';
        parent::__construct($container);

    }


}