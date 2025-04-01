### PHP Logger

## Usage Of PHP Logger Version 4

Version 4 introduces several significant improvements over previous versions:

1. **Auto File Naming**: Logs are automatically saved to files named after the PHP script that generates them
2. **Multiple Log Files**: Different scripts will create separate log files automatically
3. **Improved Resource Management**: File handles are properly managed and closed when the logger is destroyed
4. **Smart Path Detection**: Project path is automatically detected if not specified
5. **Better Error Handling**: More robust error handling for file operations

### Setup

Include the logger in your PHP file:

```php
@include_once './v4/log.php';
$logger = isset($GLOBALS['logger']) ? $GLOBALS['logger'] : function ($message, $level = 'INFO') {};
```

That's it! No additional configuration is needed.

### How It Works

The logger automatically determines which file to write logs to based on the calling script:

-   If you call the logger from `example.php`, logs will be written to `example.log`
-   If the script name cannot be determined, logs go to `default.log` (configurable)

### Advanced Usage

#### Log Different Data Types

```php
// Log strings
$logger("This is a simple message");

// Log arrays
$logger(["user" => "John", "id" => 123]);

// Log objects
$user = new stdClass();
$user->name = "John";
$user->id = 123;
$logger($user);

// Log with different levels
$logger("Debug information", "DEBUG");
$logger("Warning message", "WARNING");
$logger("Error message", "ERROR");
```

#### Log Output Format

Logs are formatted with file information included:

```log
[2024-03-19 09:45:32 example.php] [INFO]:
This is a simple message

[2024-03-19 09:45:32 example.php] [DEBUG]:
{
    "user": "John",
    "id": 123
}
```

### Implementation Details

The improved logger in version 4:

1. Creates a singleton instance automatically
2. Registers error handlers automatically
3. Uses the `__invoke()` magic method for function-like usage
4. Manages file handles for multiple log files
5. Automatically chooses log filenames based on the calling script
6. Properly closes all file handles when the script ends

### Configuration

You can modify the logger's behavior by editing `v4/log.php`:

-   Change the default log filename (default is 'default.log')
-   Modify the project path detection logic
-   Customize error handling
-   Change log format

### Benefits Over Version 3

-   **Multiple Log Files**: Each PHP script can now have its own log file
-   **Smart Naming**: No need to specify log filenames manually
-   **Resource Management**: Improved file handle management
-   **Path Detection**: Automatically detects project paths
-   **Better Structure**: More robust OOP implementation

### Example

```php
@include_once './v4/log.php';
$logger = isset($GLOBALS['logger']) ? $GLOBALS['logger'] : function ($message, $level = 'INFO') {};

// Simple usage
$logger("This is an info message");
$logger("This is an error message", "ERROR");

// Log complex data
$logger([
    "user" => "John",
    "id" => 123,
    "roles" => ["admin", "editor"]
], "DEBUG");
```

See an example in [use_v4.php](../usage/use_v4.php)
