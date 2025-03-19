<?php

namespace Akash\Logging;

use RuntimeException;

class Log
{
    private $handle;
    private $project_path;
    private $filename;

    public function __construct($filename, $project_path = 'C:\xampp\htdocs\server5640\akash\Classes\log\v1')
    {
        $this->project_path = rtrim($project_path, '/\\') . '/';
        $this->filename = $filename;

        // Ensure the directory exists
        if (!is_dir($this->project_path)) {
            mkdir($this->project_path, 0777, true);
        }

        // Try to open the file
        $file_path = $this->project_path . $filename;
        $this->handle = @fopen($file_path, 'a');

        if (!$this->handle) {
            throw new RuntimeException("Unable to open log file: $file_path");
        }
    }

    public function write($message, $level = 'INFO')
    {
        if (is_array($message) || is_object($message)) {
            $message = json_encode($message, JSON_PRETTY_PRINT);
        } elseif (!is_string($message)) {
            $message = var_export($message, true);
        }

        // Format the log entry
        $entry = sprintf(
            "[%s] [%s]: %s\n",
            date('Y-m-d G:i:s'),
            strtoupper($level),
            $message
        );

        // Write to the file
        if (is_resource($this->handle)) {
            fwrite($this->handle, $entry);
        } else {
            throw new RuntimeException("Log file handle is invalid.");
        }
    }

    public function __destruct()
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
    }

    // Optional: Singleton instance
    private static $instance;

    public static function getInstance($filename = 'main.log', $project_path = 'C:\xampp\htdocs\server5640\akash\Classes\log\v1')
    {
        if (self::$instance === null) {
            self::$instance = new self($filename, $project_path);
        }
        return self::$instance;
    }

    // New method to handle PHP errors
    public function registerErrorHandler()
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
}
