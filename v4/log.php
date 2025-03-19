<?php

namespace Akash\Classes;

use RuntimeException;

class Log
{
    private static $instance;
    private $handles = [];
    private $defaultLogFile;
    private $projectPath;

    private function __construct($defaultFilename = 'default.log', $project_path = null)
    {
        // Auto-detect the path if not explicitly provided
        if ($project_path === null) {
            $project_path = dirname(__FILE__);
        }

        $this->projectPath = rtrim($project_path, '/\\') . '/';
        $this->defaultLogFile = $defaultFilename;

        if (!is_dir($this->projectPath)) {
            mkdir($this->projectPath, 0777, true);
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

    /**
     * Get file handle for the specified log file
     * @param string $logFilename
     * @return resource
     */
    private function getHandle($logFilename)
    {
        if (!isset($this->handles[$logFilename])) {
            $logPath = $this->projectPath . $logFilename;
            $handle = @fopen($logPath, 'a');

            if (!$handle) {
                throw new RuntimeException("Unable to open log file: $logPath");
            }

            $this->handles[$logFilename] = $handle;
        }

        return $this->handles[$logFilename];
    }

    /**
     * Determine the appropriate log filename based on the caller
     * @return string
     */
    private function getLogFilename($backtrace)
    {
        $caller = isset($backtrace[1]) ? $backtrace[1] : $backtrace[0];
        $path = isset($caller['file']) ? $caller['file'] : '';
        $file = basename($path);

        // If we can determine a PHP file name, use it for the log filename
        // Otherwise use the default log file
        if (!empty($file) && preg_match('/\.php$/', $file)) {
            return str_replace('.php', '.log', $file);
        }

        return $this->defaultLogFile;
    }

    public function write($message, $level = 'INFO')
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller = isset($backtrace[1]) ? $backtrace[1] : $backtrace[0];

        $path = isset($caller['file']) ? $caller['file'] : '';
        $file = basename($path);

        // Determine which log file to use
        $logFilename = $this->getLogFilename($backtrace);
        $handle = $this->getHandle($logFilename);

        if (is_array($message) || is_object($message)) {
            $message = json_encode($message, JSON_PRETTY_PRINT);
        } elseif (!is_string($message)) {
            $message = var_export($message, true);
        }

        $entry = sprintf("[%s %s] [%s]:\n%s\n\n", date('Y-m-d H:i:s'), $file, strtoupper($level), $message);

        if (is_resource($handle)) {
            fwrite($handle, $entry);
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
        // Close all open file handles
        foreach ($this->handles as $handle) {
            if (is_resource($handle)) {
                fclose($handle);
            }
        }
    }

    public function __invoke($message, $level = 'INFO')
    {
        $this->write($message, $level);
    }
}

$GLOBALS['logger'] = Log::getInstance();
