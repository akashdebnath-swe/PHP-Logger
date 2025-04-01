<?php
// Hide errors on the browser
error_reporting(E_ALL);
ini_set("display_errors", 0);

/////// logger ////////////
@include_once '../v6/log.php';
$logger = isset($GLOBALS['logger']) ? $GLOBALS['logger'] : function ($message, $level = 'INFO') {};
/////// logger ////////////

// If the logger is a callable function, it's not fully initialized
if (!is_callable($logger) || !method_exists($logger, 'configureRotation')) {
    die('Logger v6 not properly loaded.');
}

// Set environment (affects logging behavior)
$logger->setEnvironment('development');

// Configure log rotation - enable and set max file size
// For demonstration purposes, setting a small size (10KB)
// In production, you might want to use something like 5MB (5 * 1024 * 1024)
$logger->configureRotation(true, 10 * 1024); // 10KB max file size

// Add custom log levels
$logger->addCustomLevel('SESSION', 2); // Same severity as WARNING
$logger->addCustomLevel('AUDIT', 3);   // Same severity as ERROR
$logger->addCustomLevel('DATABASE', 1); // Same severity as INFO

// Example 1: Basic logging
echo "<h2>Basic logging with v6</h2>";
$logger("This is a debug message", "DEBUG");
$logger("This is an info message", "INFO");
$logger("This is a warning message", "WARNING");
$logger("This is an error message", "ERROR");
$logger("This is a critical message", "CRITICAL");

// Example 2: Custom level logging
echo "<h2>Custom level logging</h2>";
$logger("User session started for ID: 12345", "SESSION");
$logger("User 'admin' changed settings", "AUDIT");
$logger("Connected to database", "DATABASE");

// Example 3: Log rotation demonstration
echo "<h2>Log rotation demonstration</h2>";
echo "<p>Writing multiple log entries to demonstrate log rotation...</p>";

// Log a message with decent size to quickly reach the rotation threshold
$largeMessage = str_repeat("This is a repeated message to demonstrate log rotation. ", 20);

// Write logs until we hit rotation threshold
for ($i = 1; $i <= 30; $i++) {
    $logger("Log entry #$i: $largeMessage", "INFO");
    echo "<p>Wrote log entry #$i</p>";

    // Flush to make sure logs are written immediately
    if (isset($logger->handles) && is_array($logger->handles)) {
        foreach ($logger->handles as $handle) {
            if (is_resource($handle)) {
                fflush($handle);
            }
        }
    }

    // Small pause to make the demo more visible
    usleep(100000); // 100ms
}

echo "<p>Check main.log to see the result. The file should have been rotated if it exceeded the size limit.</p>";

// Example 4: Disable rotation if needed
echo "<h2>Disabling log rotation</h2>";
$logger->configureRotation(false);
$logger("Log rotation is now disabled", "INFO");
echo "<p>Log rotation is now disabled</p>";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logger v6 Example</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            color: #333;
        }

        h1,
        h2 {
            color: #2c3e50;
        }

        p {
            margin-bottom: 10px;
        }

        pre {
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }

        .note {
            background-color: #fffacd;
            padding: 10px;
            border-left: 4px solid #ffd700;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <main>
        <h1>Logger v6 - Log Rotation Feature</h1>
        <div class="note">
            <p><strong>Note:</strong> This demo uses a small file size threshold (10KB) to demonstrate rotation. In a real application, you'd likely use a larger value (e.g., 5MB).</p>
        </div>
        <p>All log messages are saved to <code>main.log</code>. When the file exceeds the configured size limit, it will be deleted and a new log file will be created.</p>
        <p>This simpler approach allows for consistent log file naming while still managing disk space efficiently.</p>
    </main>
</body>

</html>