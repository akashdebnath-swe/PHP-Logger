### PHP Logger

## Usage Of PHP Logger Version 5

Version 5 introduces log level filtering and custom log levels, making your logging more flexible and efficient, especially in production environments.

### New Features in Version 5:

1. **Standard Log Levels**: Pre-defined severity hierarchy (DEBUG, INFO, WARNING, ERROR, CRITICAL)
2. **Custom Log Levels**: Define your own log levels with custom severity values
3. **Log Filtering**: Set minimum log level to filter out less important messages
4. **Environment Awareness**: Different logging behavior for development vs production

### Setup

Include the logger in your PHP file:

```php
@include_once './v5/log.php';
$logger = isset($GLOBALS['logger']) ? $GLOBALS['logger'] : function ($message, $level = 'INFO') {};
```

### Standard Log Levels

Version 5 includes the following standard log levels (in order of increasing severity):

1. **DEBUG** (0): Detailed debugging information
2. **INFO** (1): General information messages
3. **WARNING** (2): Warning messages that don't prevent operation
4. **ERROR** (3): Error conditions that should be addressed
5. **CRITICAL** (4): Critical conditions requiring immediate attention

Example:

```php
$logger("This is a debug message", "DEBUG");
$logger("This is an info message", "INFO");
$logger("This is a warning message", "WARNING");
$logger("This is an error message", "ERROR");
$logger("This is a critical message", "CRITICAL");
```

### Custom Log Levels

You can define your own log levels with custom severity values:

```php
// Add custom log levels with severity values
$logger->addCustomLevel('SESSION', 2);   // Same severity as WARNING
$logger->addCustomLevel('AUDIT', 3);     // Same severity as ERROR
$logger->addCustomLevel('DATABASE', 1);  // Same severity as INFO

// Use custom log levels
$logger("User session started", "SESSION");
$logger("Admin changed settings", "AUDIT");
$logger("Database query executed", "DATABASE");
```

### Log Level Filtering

Set a minimum log level to filter out less important messages:

```php
// Only log WARNING and above (WARNING, ERROR, CRITICAL)
$logger->setMinLevel('WARNING');

// These won't be logged (below WARNING level)
$logger("Debug message", "DEBUG");      // Not logged
$logger("Info message", "INFO");        // Not logged
$logger("Database message", "DATABASE"); // Not logged (if DATABASE severity < WARNING)

// These will be logged (at or above WARNING level)
$logger("Warning message", "WARNING");  // Logged
$logger("Error message", "ERROR");      // Logged
$logger("Critical message", "CRITICAL"); // Logged
$logger("Session message", "SESSION");  // Logged (if SESSION severity >= WARNING)
```

### Environment Settings

Configure the logger for different environments:

```php
// Set environment (development, testing, or production)
$logger->setEnvironment('development'); // Log everything
$logger->setEnvironment('testing');     // Use specified log levels
$logger->setEnvironment('production');  // More strict filtering

// In production, setting to DEBUG automatically changes to INFO
$logger->setMinLevel('DEBUG'); // In production, automatically becomes INFO
```

Behavior by environment:

-   **development**: Logs everything, including unknown/custom levels
-   **testing**: Follows minimum level settings, allows custom levels
-   **production**: Stricter filtering, treats unknown levels as INFO

### Complete Example

```php
@include_once './v5/log.php';
$logger = isset($GLOBALS['logger']) ? $GLOBALS['logger'] : function ($message, $level = 'INFO') {};

// Configure environment
$logger->setEnvironment('development');

// Add custom log levels
$logger->addCustomLevel('SESSION', 2);
$logger->addCustomLevel('AUDIT', 3);

// Standard level logging
$logger("Debug information", "DEBUG");
$logger("General information", "INFO");
$logger("Warning message", "WARNING");

// Custom level logging
$logger("User session started for ID: 12345", "SESSION");
$logger("User 'admin' changed settings", "AUDIT");

// Set minimum log level for filtering
$logger->setMinLevel('WARNING');

// Only messages at WARNING level and above will be logged from this point
$logger("This DEBUG message won't appear", "DEBUG");
$logger("This WARNING message will appear", "WARNING");
```

### Benefits Over Version 4

-   **Efficient Logging**: Filter out unnecessary logs based on severity
-   **Custom Levels**: Define application-specific log levels
-   **Environment Aware**: Automatically adjust logging behavior based on environment
-   **Better Debugging**: Keep detailed logs in development, minimal logs in production
-   **Improved Performance**: Avoid writing unnecessary logs to improve performance

### Best Practices

1. Use **DEBUG** for detailed diagnostic information
2. Use **INFO** for general operational information
3. Use **WARNING** for non-critical issues that should be monitored
4. Use **ERROR** for error conditions that need attention
5. Use **CRITICAL** for critical failures requiring immediate action
6. Create custom levels that map to your application's specific domains
7. Set higher minimum levels in production environments

See an example in [use_v5.php](../usage/use_v5.php)
