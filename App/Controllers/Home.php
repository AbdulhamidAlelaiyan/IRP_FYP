<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use Monolog\Formatter\HtmlFormatter;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\PsrLogMessageProcessor;
use Monolog\Processor\UidProcessor;
use Monolog\Processor\WebProcessor;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Home extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        if(Auth::getUser())
        {
            View::renderTemplate('Home/index.html.twig');
        }
        else
        {
            View::renderTemplate('Home/landing-page.html.twig');
        }
    }
}
