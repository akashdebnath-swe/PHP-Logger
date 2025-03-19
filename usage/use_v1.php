<?php
// Hide errors on the browser
error_reporting(E_ALL);
ini_set("display_errors", 0);



/////// logger ////////////
require_once '../v1/log.php';

use Akash\Logging\Log;

$logger = new Log('v1.log');
$logger->registerErrorHandler();
/////// logger ////////////



$string = 'this is a error';
$arr = [1, true, 3.5, 'name', [1, 2], ["1" => 'one']];

$logger->write($arr);
$logger->write($string, 'ERROR');
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
        hello
    </main>
</body>

</html>