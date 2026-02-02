<?php
// including files 
require 'file1.php';
require 'file2.php';

// use alias to resolve naming conflicts
use App\Models\User as ModelUser;
use App\Controllers\User as ControllerUser;

$model = new ModelUser();
$model->info();

echo "<br>";

$controller = new ControllerUser();
$controller->info();
?>
