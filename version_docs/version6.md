# Logger Version 6 Documentation

Version 6 of the PHP Logger introduces log rotation functionality while simplifying file naming. This enhancement enables automatic management of log file sizes to prevent them from consuming excessive disk space.

## Key Changes from Version 5

1. **Simplified File Naming**:

    - Removed automatic file naming based on script name
    - All logs now go to a single file (`main.log` by default)
    - Custom log file name can still be specified

2. **Log Rotation**:

    - Configurable maximum file size (default: 5MB)
    - Automatic deletion and recreation of log files when size limit is reached
    - Option to enable/disable rotation

3. **Path Auto-detection**:
    - Preserved from previous versions
    - Automatically detects the appropriate directory for log files

## Basic Usage

```php
@include_once 'path/to/v6/log.php';
$logger = isset($GLOBALS['logger']) ? $GLOBALS['logger'] : function ($message, $level = 'INFO') {};

// Log a message
$logger("This is a log message");

// Log with specified level
$logger("This is an error", "ERROR");
```

## Log Rotation Configuration

```php
// Enable log rotation with custom size (in bytes)
$logger->configureRotation(true, 5 * 1024 * 1024); // 5MB

// Disable log rotation
$logger->configureRotation(false);
```

## Custom Log File Name

```php
// During initialization (before first log)
$customLogger = \Akash\Classes\Log::getInstance('application.log');
$GLOBALS['logger'] = $customLogger;
```

## Preserved Features from Version 5

-   **Log Levels**: Standard and custom log levels with severity-based filtering
-   **Environment Awareness**: Automatic behavior adjustment based on environment
-   **Singleton Pattern**: Global logger instance accessible throughout the application
-   **Error Handling**: Automatic PHP error and exception catching

## Log Format

```
[YYYY-MM-DD H:M:S filename.php] [LEVEL]:
message
```

## How Log Rotation Works

1. When a write operation is attempted, the logger checks if the log file exists and exceeds the configured size limit
2. If the size is exceeded, the file is closed and deleted
3. A new log file with the same name is created when the next log message is written
4. This simple approach ensures logs don't grow indefinitely while maintaining a consistent file naming scheme

## Configuration Options

| Method                                  | Description                                            |
| --------------------------------------- | ------------------------------------------------------ |
| `configureRotation($enabled, $maxSize)` | Configure log rotation settings                        |
| `setMinLevel($level)`                   | Set minimum log level threshold                        |
| `addCustomLevel($level, $severity)`     | Add a custom log level                                 |
| `setEnvironment($env)`                  | Set the environment (development, testing, production) |

## Complete Example

```php
@include_once 'path/to/v6/log.php';
$logger = isset($GLOBALS['logger']) ? $GLOBALS['logger'] : function ($message, $level = 'INFO') {};

// Configure the logger
$logger->setEnvironment('production');
$logger->setMinLevel('WARNING');
$logger->configureRotation(true, 10 * 1024 * 1024); // 10MB size limit
$logger->addCustomLevel('AUDIT', 3); // Custom level with ERROR severity

// Log messages
$logger("This won't be logged", "DEBUG"); // Below minimum level
$logger("This will be logged", "WARNING"); // At minimum level
$logger("This will be logged", "AUDIT"); // Custom level at ERROR severity
```

## Best Practices

1. **Size Configuration**: Choose an appropriate file size based on your application's logging volume

    - Development: Smaller sizes (1-5MB) for faster troubleshooting
    - Production: Larger sizes (10-50MB) for less frequent rotation

2. **Log Levels**: Use appropriate log levels and configure minimum level based on environment

    - Development: DEBUG or INFO
    - Production: WARNING or higher for most applications

3. **Custom File Names**: Use descriptive names if working with custom log files:

    - `application.log`: General application logs
    - `errors.log`: Only error-level messages
    - `system.log`: System operations

4. **Error Handling**: Always check if the logger is properly loaded:

```php
$logger = isset($GLOBALS['logger']) ? $GLOBALS['logger'] : function ($message, $level = 'INFO') {};
```
