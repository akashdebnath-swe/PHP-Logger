# PHP Logger

A simple, flexible and powerful logging utility for PHP applications. This project offers six versions with increasingly improved features and ease of use.

## Overview

PHP Logger provides an easy way to log messages, errors, and debug information in your PHP applications. It supports different log levels, automatic error handling, and can log various data types including strings, arrays, and objects.

## Features

-   **Multiple versions**: Six different versions with progressive improvements
-   **Flexible logging**: Support for different log levels (INFO, ERROR, WARNING, etc.)
-   **Data type support**: Log strings, arrays, objects, and other data types
-   **Error handling**: Automatic PHP error and exception catching
-   **Singleton pattern**: Easy to use globally in your application
-   **File-based logging**: All logs are stored in customizable log files
-   **Auto-file naming**: Version 4 automatically creates log files named after the PHP scripts
-   **Multiple log files**: Support for multiple log files with proper resource management
-   **Log level filtering**: Version 5 adds support for filtering logs by severity level
-   **Custom log levels**: Version 5 allows defining custom application-specific log levels
-   **Environment-aware**: Version 5 automatically adjusts logging behavior based on environment
-   **Log rotation**: Version 6 adds automatic log file rotation based on file size (NEW)

## Installation

1. Clone the repository:

    ```
    git clone https://github.com/akashdebnath-swe/PHP-Logger
    ```

2. Include the desired version in your PHP project:
    ```php
    require_once 'path/to/vX/log.php'; // where X is the version number (1, 2, 3, 4, 5, or 6)
    ```

## Usage

### Version 1

The original version with basic functionality.

```php
require_once './v1/log.php';

use Akash\Logging\Log;

// Create a new logger instance
$logger = new Log('v1.log');
$logger->registerErrorHandler();

// Log different types of data
$logger->write("This is an info message.");
$logger->write("This is an error message.", "ERROR");

// Log arrays or objects
$arr = [1, true, 3.5, 'name', [1, 2], ["1" => 'one']];
$logger->write($arr);

// Singleton usage
$singletonLogger = Log::getInstance();
$singletonLogger->write("Reusing the same logger instance.");
```

[Detailed documentation for Version 1](version_docs/version1.md)

### Version 2

Improved with global accessibility and automatic error handling.

```php
require_once './v2/log.php';

// Logger is now available globally
$logger = $GLOBALS['logger'];

// Log messages
$logger->write("This is an info message.");
$logger->write("This is an error message.", "ERROR");
```

[Detailed documentation for Version 2](version_docs/version2.md)

### Version 3

The more streamlined version with function-like usage.

```php
@include_once './v3/log.php';
$logger = isset($GLOBALS['logger']) ? $GLOBALS['logger'] : function ($message, $level = 'INFO') {};

// Function-like usage
$logger("This is an info message.");
$logger("This is an error message.", "ERROR");
```

[Detailed documentation for Version 3](version_docs/version3.md)

### Version 4

The more advanced version with automatic log file naming and multiple file support.

```php
@include_once './v4/log.php';
$logger = isset($GLOBALS['logger']) ? $GLOBALS['logger'] : function ($message, $level = 'INFO') {};

// Function-like usage with automatic file naming
$logger("This is an info message");
$logger("This is an error message", "ERROR");

// The logs will be saved in a file named after your PHP script
// For example, if this code is in example.php, logs go to example.log
```

[Detailed documentation for Version 4](version_docs/version4.md)

### Version 5

The latest version with log level filtering and custom log levels.

```php
@include_once './v5/log.php';
$logger = isset($GLOBALS['logger']) ? $GLOBALS['logger'] : function ($message, $level = 'INFO') {};

// Configure environment and log levels
$logger->setEnvironment('development');
$logger->setMinLevel('WARNING'); // Only log WARNING and above
$logger->addCustomLevel('SESSION', 2); // Custom level with WARNING severity

// Standard and custom level logging
$logger("This is a warning", "WARNING"); // Will be logged
$logger("This is debug info", "DEBUG");  // Won't be logged (below WARNING)
$logger("User session", "SESSION");      // Will be logged (same as WARNING)
```

[Detailed documentation for Version 5](version_docs/version5.md)

### Version 6

The latest version with log rotation and simplified file naming.

```php
@include_once './v6/log.php';
$logger = isset($GLOBALS['logger']) ? $GLOBALS['logger'] : function ($message, $level = 'INFO') {};

// Configure log rotation (enable and set max size to 5MB)
$logger->configureRotation(true, 5 * 1024 * 1024);

// Log messages to a single file (main.log by default)
$logger("This is an info message");
$logger("This is an error message", "ERROR");

// When the log file reaches the size limit, it will be automatically
// deleted and a new empty log file will be created
```

[Detailed documentation for Version 6](version_docs/version6.md)

## Versioning Evolution

1. **Version 1**: Basic logging with explicit instantiation
2. **Version 2**: Global accessibility with `$GLOBALS['logger']`
3. **Version 3**: Function-like usage with the `__invoke()` magic method
4. **Version 4**: Smart file naming and multiple log file support
5. **Version 5**: Log level filtering, custom log levels and environment awareness
6. **Version 6**: Log rotation and simplified file naming

## Log Format

### Version 1 & 2

```
[YYYY-MM-DD H:M:S] [LEVEL]: message
```

### Version 3

```
[YYYY-MM-DD H:M:S filename.php] [LEVEL]:
message
```

### Version 4

```
[YYYY-MM-DD H:M:S filename.php] [LEVEL]:
message
```

### Version 5

```
[YYYY-MM-DD H:M:S filename.php] [LEVEL]:
message
```

### Version 6

```
[YYYY-MM-DD H:M:S filename.php] [LEVEL]:
message
```

## Configuration

Each version requires slightly different configuration:

-   **Version 1**: Configure in the instance creation or in the log.php file
-   **Version 2**: Configure directly in the v2/log.php file
-   **Version 3**: Configure directly in the v3/log.php file
-   **Version 4**: Configure directly in the v4/log.php file
-   **Version 5**: Configure through API calls after initialization:
    ```php
    $logger->setMinLevel('WARNING');
    $logger->setEnvironment('production');
    $logger->addCustomLevel('AUDIT', 3);
    ```

## Sample Usage Files

Example implementation files are included:

-   [use_v1.php](usage/use_v1.php) - Example using Version 1
-   [use_v2.php](usage/use_v2.php) - Example using Version 2
-   [use_v3.php](usage/use_v3.php) - Example using Version 3
-   [use_v4.php](usage/use_v4.php) - Example using Version 4
-   [use_v5.php](usage/use_v5.php) - Example using Version 5
-   [use_v6.php](usage/use_v6.php) - Example using Version 6

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Author

Akash Debnath <br>
[GitHub](https://github.com/akashdebnath-swe) <br>
[Twitter](https://x.com/akash_swe) <br>
