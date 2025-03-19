<?php
// Hide errors on the browser
error_reporting(E_ALL);
ini_set("display_errors", 0);


/////// logger ////////////
require_once '../v2/log.php';
$logger = $GLOBALS['logger'];
/////// logger ////////////


$logger->write("This is an info message.");
$logger->write("This is an error message.", "ERROR");
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
        hello v2
    </main>
</body>

</html>