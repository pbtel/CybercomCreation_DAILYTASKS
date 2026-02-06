<?php
// Include the files with namespaces
require 'Services/Logger.php';
require 'Utils/Logger.php';

// use alias
use Services\Logger as ServiceLogger;
use Utils\Logger as UtilLogger;

// Create instances and call methods
$serviceLogger = new ServiceLogger();
$serviceLogger->log();

echo "<br>";

// Create instance of Utils Logger
$utilLogger = new UtilLogger();
$utilLogger->log();
?>
