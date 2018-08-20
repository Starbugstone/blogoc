<?php
namespace Core;

abstract class AdminController extends Controller{

    protected $admin;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->admin = new Admin($this->container);
    }


}