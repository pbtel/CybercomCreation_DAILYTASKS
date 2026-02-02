<?php

// Include the files with namespaces
require 'one.php';
require 'two.php';

$paymentProcess = new Payment\Process();
$paymentProcess->pay();

echo "<br>";

$orderProcess = new Order\Process();
$orderProcess->pay();
?>
