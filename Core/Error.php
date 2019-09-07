<?php

namespace Core;

use App\Auth;
use Monolog\Formatter\HtmlFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\PsrLogMessageProcessor;
use Monolog\Processor\UidProcessor;
use Monolog\Processor\WebProcessor;

/**
 * Error and exception handler
 *
 * PHP version 7.0
 */
class Error
{

    /**
     * Error handler. Convert all errors to Exceptions by throwing an ErrorException.
     *
     * @param int $level  Error level
     * @param string $message  Error message
     * @param string $file  Filename the error was raised in
     * @param int $line  Line number in the file
     *
     * @return void
     */
    public static function errorHandler($level, $message, $file, $line)
    {
        if (error_reporting() !== 0) {  // to keep the @ operator working
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Exception handler.
     *
     * @param Exception $exception  The exception
     *
     * @return void
     */
    public static function exceptionHandler($exception)
    {
        // Code is 404 (not found) or 500 (general error)
        $code = $exception->getCode();
        if ($code != 404) {
            $code = 500;
        }
        http_response_code($code);

        if (\App\Config::SHOW_ERRORS) {
            echo "<h1>Fatal error</h1>";
            echo "<p>Uncaught exception: '" . get_class($exception) . "'</p>";
            echo "<p>Message: '" . $exception->getMessage() . "'</p>";
            echo "<p>Stack trace:<pre>" . $exception->getTraceAsString() . "</pre></p>";
            echo "<p>Thrown in '" . $exception->getFile() . "' on line " . $exception->getLine() . "</p>";
        } else {
//            $log = dirname(__DIR__) . '/logs/' . date('Y-m-d') . '.txt';
//            ini_set('error_log', $log);
//
//            $message = "Uncaught exception: '" . get_class($exception) . "'";
//            $message .= " with message '" . $exception->getMessage() . "'";
//            $message .= "\nStack trace: " . $exception->getTraceAsString();
//            $message .= "\nThrown in '" . $exception->getFile() . "' on line " . $exception->getLine();
//
//            error_log($message);

            $Errorlogger = new Logger('Exception-Handler');
            $fileHandler = new StreamHandler(dirname(__DIR__) . '/logs/ERROR.html', Logger::ERROR);
            $fileHandler->setFormatter(new HtmlFormatter());
            $Errorlogger->pushHandler($fileHandler);
            $Errorlogger->pushProcessor(new WebProcessor());
            $Errorlogger->pushProcessor(new UidProcessor());
            $Errorlogger->pushProcessor(new MemoryUsageProcessor());
            $Errorlogger->pushProcessor(new PsrLogMessageProcessor());
            $currentUser = Auth::getUser();
            $userEmail = $currentUser->email ?? '';

            $Errorlogger->addError('Exception Occurred',
                ['user-email' => $userEmail,
                    'exception-class' => get_class($exception),
                    'exception-message' => $exception->getMessage(),
                    'exception-trace' => $exception->getTraceAsString(),
                    'exception-file' => $exception->getFile(),
                    'exception-line' => $exception->getLine()]);

            View::renderTemplate("$code.html.twig");
        }
    }
}
