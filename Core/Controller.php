<?php

namespace Core;

use \App\Auth;
use \App\Flash;
use GuzzleHttp\Psr7\Stream;
use Monolog\Formatter\HtmlFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\PsrLogMessageProcessor;
use Monolog\Processor\UidProcessor;
use Monolog\Processor\WebProcessor;

/**
 * Base controller
 *
 * PHP version 7.0
 */
abstract class Controller
{

    /**
     * Parameters from the matched route
     * @var array
     */
    protected $route_params = [];

    /**
     * Loggers for the controller
     * @var Logger
     */
    protected $logger;

    /**
     * Class constructor
     *
     * @param array $route_params  Parameters from the route
     *
     * @return void
     */
    public function __construct($route_params)
    {
        // Set the parameters
        $this->route_params = $route_params;
        // Set the loggers
        $this->logger = new Logger('Controller');
        // Info file handler
        $infoFileHandler = new StreamHandler(dirname(__DIR__) . '/logs/INFO.html', Logger::INFO);
        $infoFileHandler->setFormatter(new HtmlFormatter());
        $this->logger->pushHandler($infoFileHandler);
        // Debug file handler
        $debugFileHandler = new StreamHandler(dirname(__DIR__) . '/logs/DEBUG.html', Logger::DEBUG);
        $debugFileHandler->setFormatter(new HtmlFormatter());
        $this->logger->pushHandler($debugFileHandler);
        // Notice file handler
        $noticeFileHandler = new StreamHandler(dirname(__DIR__) . '/logs/NOTICE.html', Logger::NOTICE);
        $noticeFileHandler->setFormatter(new HtmlFormatter());
        $this->logger->pushHandler($noticeFileHandler);
        // Pushing processors of controller logger
        $this->logger->pushProcessor(new WebProcessor());
        $this->logger->pushProcessor(new UidProcessor());
        $this->logger->pushProcessor(new MemoryUsageProcessor());
        $this->logger->pushProcessor(new PsrLogMessageProcessor());
        $this->logger->pushProcessor(new IntrospectionProcessor());
    }

    /**
     * Magic method called when a non-existent or inaccessible method is
     * called on an object of this class. Used to execute before and after
     * filter methods on action methods. Action methods need to be named
     * with an "Action" suffix, e.g. indexAction, showAction etc.
     *
     * @param string $name Method name
     * @param array $args Arguments passed to the method
     *
     * @return void
     * @throws \Exception
     */
    public function __call($name, $args)
    {
        $method = $name . 'Action';

        if (method_exists($this, $method)) {
            $userEmail = Auth::getUserEmailForLogger();
            $this->logger->addDebug($method . ' action was requested', ['user-email' => $userEmail]); // TODO: add the arguments in the log message.

            // Call the action filters
            if ($this->before() !== false) {
                $this->logger->addDebug($method . ' action filter (before) passed', ['user-email' => $userEmail]);
                call_user_func_array([$this, $method], $args);
                $this->logger->addDebug($method . ' action executed', ['user-email' => $userEmail]);
                $this->after();
                $this->logger->addDebug($method . ' action filter (after) passed', ['user-email' => $userEmail]);
            }
        } else {
            throw new \Exception("Method $method not found in controller " . get_class($this));
        }
    }

    /**
     * Before filter - called before an action method.
     *
     * @return void
     */
    protected function before()
    {
    }

    /**
     * After filter - called after an action method.
     *
     * @return void
     */
    protected function after()
    {
    }

    /**
     * Redirect to a different page
     *
     * @param string $url  The relative URL
     *
     * @return void
     */
    public function redirect($url)
    {
        header('Location: http://' . $_SERVER['HTTP_HOST'] . $url, true, 303);
        exit;
    }

    /**
     * Require the user to be logged in before giving access to the requested page.
     * Remember the requested page for later, then redirect to the login page.
     *
     * @return void
     */
    public function requireLogin()
    {
        if (! Auth::getUser()) {

            //Flash::addMessage('Please login to access that page');
            Flash::addMessage('Please login to access that page', Flash::INFO);

            Auth::rememberRequestedPage();

            $this->redirect('/login');
        }
    }
}
