<?php
require 'A.php';
require 'B.php';

$dbConnection = new Library\Database\Connection();
$dbConnection->connect();

echo "<br>";

$apiConnection = new Library\API\Connection();
$apiConnection->connect();
?>
