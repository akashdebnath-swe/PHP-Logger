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
* @version 1.0
* @link https://github.com/akashdebnath-swe/PHP-Logger
* @file log.php
* @created at 2025
*/

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
