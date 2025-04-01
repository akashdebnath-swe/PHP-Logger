<?php
/*
* PHP Logger
*
* A simple, flexible, and powerful logging utility
*
* PHP Logger is distributed under the MIT License
* Copyright (C) 2025 Akash Debnath
*
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included in
* all copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
* THE SOFTWARE.
*
* @package PHP-Logger
* @author Akash Debnath
* @copyright 2025 Akash Debnath
* @license MIT http://opensource.org/licenses/MIT
* @version 4.0
* @link https://github.com/akashdebnath-swe/PHP-Logger
* @file log.php
* @created at 2025
*/


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
