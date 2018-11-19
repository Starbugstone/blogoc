<?php

namespace Core;

/**
 * Application configuration
 *
 * PHP version 7
 */
class Config
{
    /*
     * -----------------------------------------------------
     *                     DATABASE CONFIG
     * -----------------------------------------------------
     */

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

    /**
     * If we use a table prefix, put it here (without the underscore).
     * the tables will then be accessed via prefix_table.
     * @var string
     */
    const TABLE_PREFIX = 'boc';

    /**
     * Image upload folders
     * These folders must be set to read / write on the server
     */
    const UPLOADED_IMAGES = "uploaded_images/";
    const CONFIG_IMAGES = "config_images/";
    const USER_IMAGES = "user_images/";

    /**
     * Google reCapcha keys for the contact form
     */
    const GOOGLE_RECAPCHA_SECRET_KEY = "";
    const GOOGLE_RECAPCHA_PUBLIC_KEY = "";
}
