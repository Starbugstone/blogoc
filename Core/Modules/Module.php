<?php

namespace Core\Modules;

use Core\Container;

/**
 * The abstract class for all the modules. The construct container is needed for dependency injection
 * Class Module
 * @package Core\Modules
 */
abstract class Module
{
    /**
     * @var \Core\Container dependency injector
     */
    protected $container;

    /**
     * Auth constructor.
     * @param $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
}