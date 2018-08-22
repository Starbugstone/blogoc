<?php

namespace Core;

/**
 * Application configuration
 *
 * PHP version 7
 */
class Config
{
    /**
     * Database host
     * @var string
     */
    const DB_HOST = 'localhost';

    /**
     * Database name
     * @var string
     */
    const DB_NAME = 'blogoc';

    /**
     * Database user
     * @var string
     */
    const DB_USER = 'root';

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = '';

    /**
     * Show or hide error messages on screen
     * also used for prod only elements like the cache
     * @var bool
     */
    const DEV_ENVIRONMENT = true;
}