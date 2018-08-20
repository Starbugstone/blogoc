<?php
namespace Core;

abstract class AdminController extends Controller{


    public function __construct(Container $container)
    {
        $this->loadModules[] = 'Admin';
        parent::__construct($container);
        //$this->admin = new Admin($this->container);
    }


}