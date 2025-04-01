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
* @version 6.0
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
    private $logFile = 'main.log';
    private $projectPath;

    // Log rotation settings
    private $maxFileSize = 5242880; // 5MB default max file size
    private $rotationEnabled = true;

    // Standard log levels in order of severity
    private $standardLevels = [
        'DEBUG' => 0,
        'INFO' => 1,
        'WARNING' => 2,
        'ERROR' => 3,
        'CRITICAL' => 4
    ];

    // Custom log levels added by the user
    private $customLevels = [];

    // Current minimum log level
    private $minLevel = 'DEBUG'; // Default to lowest level (log everything)

    // Environment setting (affects logging behavior)
    private $environment = 'development'; // Options: development, testing, production

    private function __construct($logFilename = 'main.log', $project_path = null)
    {
        // Auto-detect the path if not explicitly provided
        if ($project_path === null) {
            $project_path = dirname(__FILE__);
        }

        $this->projectPath = rtrim($project_path, '/\\') . '/';
        $this->logFile = $logFilename;

        if (!is_dir($this->projectPath)) {
            mkdir($this->projectPath, 0777, true);
        }

        $this->registerErrorHandler();
    }

    public static function getInstance($logFilename = 'main.log', $project_path = null)
    {
        if (self::$instance === null) {
            self::$instance = new self($logFilename, $project_path);
        }
        return self::$instance;
    }

    /**
     * Configure log rotation settings
     * @param bool $enabled Enable/disable log rotation
     * @param int $maxSize Maximum file size in bytes before rotation
     * @return self
     */
    public function configureRotation($enabled = true, $maxSize = null)
    {
        $this->rotationEnabled = $enabled;

        if ($maxSize !== null) {
            if (!is_numeric($maxSize) || $maxSize <= 0) {
                throw new RuntimeException("Max file size must be a positive number");
            }
            $this->maxFileSize = (int) $maxSize;
        }

        return $this;
    }

    /**
     * Set the minimum log level
     * @param string $level The minimum level to log
     * @return self
     */
    public function setMinLevel($level)
    {
        $level = strtoupper($level);

        // Check if it's a valid level
        if (!$this->isValidLevel($level)) {
            throw new RuntimeException("Invalid log level: $level");
        }

        $this->minLevel = $level;
        return $this;
    }

    /**
     * Add a custom log level
     * @param string $level The custom level name
     * @param int $severity The severity value (0-100, higher means more severe)
     * @return self
     */
    public function addCustomLevel($level, $severity = 1)
    {
        $level = strtoupper($level);

        // Don't overwrite standard levels
        if (isset($this->standardLevels[$level])) {
            throw new RuntimeException("Cannot override standard log level: $level");
        }

        $this->customLevels[$level] = $severity;
        return $this;
    }

    /**
     * Set the environment (affects logging behavior)
     * @param string $env The environment (development, testing, production)
     * @return self
     */
    public function setEnvironment($env)
    {
        $validEnvs = ['development', 'testing', 'production'];
        $env = strtolower($env);

        if (!in_array($env, $validEnvs)) {
            throw new RuntimeException("Invalid environment: $env. Valid options are: " . implode(', ', $validEnvs));
        }

        $this->environment = $env;

        // Auto-set minimum log level based on environment
        if ($env === 'production' && $this->minLevel === 'DEBUG') {
            $this->minLevel = 'INFO'; // In production, default to INFO level
        }

        return $this;
    }

    /**
     * Check if a level is valid (standard or custom)
     * @param string $level The level to check
     * @return bool
     */
    private function isValidLevel($level)
    {
        return isset($this->standardLevels[$level]) || isset($this->customLevels[$level]);
    }

    /**
     * Check if a message at the given level should be logged
     * @param string $level The message level
     * @return bool Whether the message should be logged
     */
    private function shouldLog($level)
    {
        $level = strtoupper($level);

        // In development mode, log everything
        if ($this->environment === 'development') {
            return true;
        }

        // Custom levels we don't know about are treated as INFO level
        if (!$this->isValidLevel($level)) {
            // In production, unknown levels are logged as INFO
            if ($this->environment === 'production') {
                $level = 'INFO';
            } else {
                // In other environments, log custom levels
                return true;
            }
        }

        // Get severity values
        $levelSeverity = isset($this->standardLevels[$level])
            ? $this->standardLevels[$level]
            : $this->customLevels[$level];

        $minLevelSeverity = isset($this->standardLevels[$this->minLevel])
            ? $this->standardLevels[$this->minLevel]
            : $this->customLevels[$this->minLevel];

        // Log if level severity is >= min level severity
        return $levelSeverity >= $minLevelSeverity;
    }

    /**
     * Perform log rotation if needed
     * @param string $logPath Full path to the log file
     * @return void
     */
    private function rotateLogIfNeeded($logPath)
    {
        if (!$this->rotationEnabled) {
            return;
        }

        // Check if file exists and exceeds size limit
        if (file_exists($logPath) && filesize($logPath) >= $this->maxFileSize) {
            // Close the handle if it's open
            if (isset($this->handles[$this->logFile]) && is_resource($this->handles[$this->logFile])) {
                fclose($this->handles[$this->logFile]);
                unset($this->handles[$this->logFile]);
            }

            // Delete the file and a new one will be created on next write
            @unlink($logPath);
        }
    }

    /**
     * Get file handle for the log file
     * @return resource
     */
    private function getHandle()
    {
        $logPath = $this->projectPath . $this->logFile;

        // Check if we need to rotate the log first
        $this->rotateLogIfNeeded($logPath);

        if (!isset($this->handles[$this->logFile])) {
            $handle = @fopen($logPath, 'a');

            if (!$handle) {
                throw new RuntimeException("Unable to open log file: $logPath");
            }

            $this->handles[$this->logFile] = $handle;
        }

        return $this->handles[$this->logFile];
    }

    /**
     * Write a message to the log
     * @param mixed $message The message to log
     * @param string $level The log level
     * @return void
     */
    public function write($message, $level = 'INFO')
    {
        $level = strtoupper($level);

        // Check if we should log this message based on level
        if (!$this->shouldLog($level)) {
            return;
        }

        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller = isset($backtrace[1]) ? $backtrace[1] : $backtrace[0];

        $path = isset($caller['file']) ? $caller['file'] : '';
        $file = basename($path);

        // Get the file handle (with rotation if needed)
        $handle = $this->getHandle();

        if (is_array($message) || is_object($message)) {
            $message = json_encode($message, JSON_PRETTY_PRINT);
        } elseif (!is_string($message)) {
            $message = var_export($message, true);
        }

        $entry = sprintf("[%s %s] [%s]:\n%s\n\n", date('Y-m-d H:i:s'), $file, $level, $message);

        if (is_resource($handle)) {
            fwrite($handle, $entry);
        }
    }

    /**
     * Register error and exception handlers
     */
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

    /**
     * Clean up file handles
     */
    public function __destruct()
    {
        foreach ($this->handles as $handle) {
            if (is_resource($handle)) {
                fclose($handle);
            }
        }
    }

    /**
     * Magic method to allow using the logger as a function
     * @param mixed $message The message to log
     * @param string $level The log level
     * @return self
     */
    public function __invoke($message, $level = 'INFO')
    {
        $this->write($message, $level);
        return $this;
    }
}

// Create a global singleton instance
if (!isset($GLOBALS['logger'])) {
    $GLOBALS['logger'] = Log::getInstance();
}
