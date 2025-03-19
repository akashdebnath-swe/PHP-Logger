<?php
// Include the logger
require_once 'log.php';

// Get the logger instance
$logger = isset($GLOBALS['logger']) ? $GLOBALS['logger'] : function ($message, $level = 'INFO') {};

// Log some messages
$logger('This is a test message from test_logger.php');
$logger('This should go to test_logger.log', 'DEBUG');
$logger(['array' => 'test', 'nested' => ['data' => true]], 'INFO');

echo "Logging complete. Check test_logger.log for results.\n";
