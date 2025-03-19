<?php
// Hide errors on the browser
error_reporting(E_ALL);
ini_set("display_errors", 0);


/////// logger ////////////
@include_once '../v4/log.php';
$logger = isset($GLOBALS['logger']) ? $GLOBALS['logger'] : function ($message, $level = 'INFO') {};
/////// logger ////////////


// Log some messages
$logger("This is an info message");
$logger("This is an error message", "ERROR");
$logger(["user" => "John", "id" => 123, "roles" => ["admin", "editor"]], "DEBUG");

// The log will be saved in a file named use_v4.log
// since the logger automatically uses the PHP filename
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logger v4 Example</title>
</head>

<body>
    <main>
        <h1>Hello v4</h1>
        <p>Check the use_v4.log file for log entries</p>
    </main>
</body>

</html>