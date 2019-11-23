<?php

namespace App;

/**
 * Application configuration
 *
 * PHP version 7.0
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
    const DB_NAME = 'IRP';

    /**
     * Database user
     * @var string
     */
    const DB_USER = 'root';

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = 'root';

    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = true;

    /**
     * Secret key for hashing
     * @var boolean
     */
    const SECRET_KEY = 'gWSDiNtyInt1WSMUXB0osXB9tcA8VGOR';

    /**
     * Mailgun API key
     *
     * @var string
     */
    const MAILGUN_API_KEY = '3c855ccef662c332c9bf67e2c1f7b274-19f318b0-4c35faaa';

    /**
     * Mailgun domain
     *
     * @var string
     */
    const MAILGUN_DOMAIN = 'sandboxb5a9fb98392e4b7da7a95660e08cbd54.mailgun.org';

    /**
     * Directory of Web Application on the Web Server filesystem
     *
     * @var string
     */
    const APP_DIRECTORY = '/var/www/';

    /**
     * The name of the website
     *
     * @var string
     */
    const WEBSITE_NAME = 'WebsiteName';
}
