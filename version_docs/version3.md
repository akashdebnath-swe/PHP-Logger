### PHP Logger

### Usage Of PHP Logger Version 3

-   In version 3 few things are changed

before:

```php
require_once './v2/log.php';

$logger = $GLOBALS['logger'];

$logger->write("This is an info message.");
$logger->write("This is an error message.", "ERROR");
```

Now:

-   using `@include_once` so that if you delete the `log.php` by mistake it won't throw error.
-   Also if it didn't get the logger in global variable it will create a empty funtion to prevent error.
-   We are using the **magic method `__invoke()`** in your `Log` class.
-   This will allow us to use `$logger('message', 'level')` directly like a function.

```php
@include_once './v3/log.php';
$logger = isset($GLOBALS['logger']) ? $GLOBALS['logger'] : function ($message, $level = 'INFO') {};
```

```php
$logger("This is an info message.");
$logger("This is an error message.", "ERROR");
```

> Writing `$logger->write()` was a pain haha!.

`OUTPUT`

-   The output is also a little bit different
-   Output now show's the file name from where the log is coming.

Syntax:

```log
[date time file_name] [Level]
log-message
```

```log
[2025-03-19 09:28:19 use_v3.php] [INFO]:
This is an info message.

[2025-03-19 09:28:19 use_v3.php] [ERROR]:
This is an error message.
```

See an example in [use_v3.php](../usage/use_v3.php)
