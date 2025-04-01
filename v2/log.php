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
* @version 2.0
* @link https://github.com/akashdebnath-swe/PHP-Logger
* @file log.php
* @created at 2025
*/

namespace Akash\Classes;

use RuntimeException;

class Log
{
    private static $instance;
    private $handle;

    private function __construct($filename = 'v2.log', $project_path = 'C:\xampp\htdocs\server5640\akash\Classes\log\v2')
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
        if (is_array($message) || is_object($message)) {
            $message = json_encode($message, JSON_PRETTY_PRINT);
        } elseif (!is_string($message)) {
            $message = var_export($message, true);
        }

        $entry = sprintf("[%s] [%s]: %s\n", date('Y-m-d G:i:s'), strtoupper($level), $message);

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
}

$GLOBALS['logger'] = Log::getInstance();
