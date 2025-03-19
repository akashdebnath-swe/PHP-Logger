<?php

namespace Akash\Classes;

use RuntimeException;

class Log
{
    private static $instance;
    private $handle;

    private function __construct($filename = 'v4.log', $project_path = 'C:\xampp\htdocs\server5640\akash\Classes\log\v4')
    {
        $project_path = rtrim($project_path, '/\\') . '/';

        if (!is_dir($project_path)) {
            mkdir($project_path, 0777, true);
        }

        $file_path = $project_path . $filename;
        $this->handle = @fopen($file_path, 'a');

        if (!$this->handle) {
            throw new RuntimeException("Unable to open log file: $file_path");
        }

        $this->registerErrorHandler();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function write($message, $level = 'INFO')
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller = isset($backtrace[1]) ? $backtrace[1] : $backtrace[0];

        $path = isset($caller['file']) ? $caller['file'] : '';
        $file = basename($path);

        if (is_array($message) || is_object($message)) {
            $message = json_encode($message, JSON_PRETTY_PRINT);
        } elseif (!is_string($message)) {
            $message = var_export($message, true);
        }

        $entry = sprintf("[%s %s] [%s]:\n%s\n\n", date('Y-m-d H:i:s'), $file, strtoupper($level), $message);

        if (is_resource($this->handle)) {
            fwrite($this->handle, $entry);
        }
    }

    private function registerErrorHandler()
    {
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            $message = "Error: [$errno] $errstr - $errfile:$errline";
            $this->write($message, 'ERROR');
        });

        set_exception_handler(function ($exception) {
            $message = "Uncaught Exception: " . $exception->getMessage() .
                " in " . $exception->getFile() . ":" . $exception->getLine();
            $this->write($message, 'EXCEPTION');
        });
    }

    public function __destruct()
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
    }

    public function __invoke($message, $level = 'INFO')
    {
        $this->write($message, $level);
    }
}

$GLOBALS['logger'] = Log::getInstance();
