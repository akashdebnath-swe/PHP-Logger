<?php
// Hide errors on the browser
error_reporting(E_ALL);
ini_set("display_errors", 0);


/////// logger ////////////
@include_once '../v3/log.php';
$logger = isset($GLOBALS['logger']) ? $GLOBALS['logger'] : function ($message, $level = 'INFO') {};
/////// logger ////////////


$logger("This is an info message.");
$logger("This is an error message.", "ERROR");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <main>
        hello v3
    </main>
</body>

</html>