<?php
// Hide errors on the browser
error_reporting(E_ALL);
ini_set("display_errors", 0);


/////// logger ////////////
@include_once '../v5/log.php';
$logger = isset($GLOBALS['logger']) ? $GLOBALS['logger'] : function ($message, $level = 'INFO') {};
/////// logger ////////////

// If the logger is a callable function, it's not fully initialized
if (!is_callable($logger) || !method_exists($logger, 'setMinLevel')) {
    die('Logger v5 not properly loaded.');
}

// Set environment (affects logging behavior)
// Options: development, testing, production
$logger->setEnvironment('development');

// Add some custom log levels
$logger->addCustomLevel('SESSION', 2); // Same severity as WARNING
$logger->addCustomLevel('AUDIT', 3);   // Same severity as ERROR
$logger->addCustomLevel('DATABASE', 1); // Same severity as INFO

// Example 1: Log at different standard levels
$logger("This is a debug message", "DEBUG");
$logger("This is an info message", "INFO");
$logger("This is a warning message", "WARNING");
$logger("This is an error message", "ERROR");
$logger("This is a critical message", "CRITICAL");

// Example 2: Log with custom levels
$logger("User session started for ID: 12345", "SESSION");
$logger("User 'admin' changed settings", "AUDIT");
$logger("Connected to database", "DATABASE");

// Example 3: Set minimum log level and demonstrate filtering
echo "<h2>Testing log level filtering</h2>";

echo "<p>Setting minimum level to WARNING...</p>";
$logger->setMinLevel('WARNING');

// These won't be logged because they're below WARNING level
$logger("This DEBUG message should NOT appear in the log", "DEBUG");
$logger("This INFO message should NOT appear in the log", "INFO");
$logger("This DATABASE message should NOT appear in the log", "DATABASE");

// These will be logged because they're at or above WARNING level
$logger("This WARNING message should appear in the log", "WARNING");
$logger("This ERROR message should appear in the log", "ERROR");
$logger("This CRITICAL message should appear in the log", "CRITICAL");
$logger("This SESSION message should appear in the log", "SESSION"); // Same severity as WARNING
$logger("This AUDIT message should appear in the log", "AUDIT");    // Same severity as ERROR

// Example 4: Change environment to production
echo "<p>Setting environment to production...</p>";
$logger->setEnvironment('production');

// Even though min level is WARNING, setting to production auto-changes to INFO if it was on DEBUG
$logger->setMinLevel('DEBUG'); // In production, this will be auto-set to INFO

// These logs with non-standard levels will be treated as INFO in production
$logger("This custom level is treated as INFO in production", "CUSTOM_LEVEL");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logger v5 Example</title>
</head>

<body>
    <main>
        <h1>Hello v5 - Log Level Filtering</h1>
        <p>Check the use_v5.log file for log entries.</p>
        <p>Not all messages will appear - we're demonstrating level filtering!</p>
    </main>
</body>

</html>