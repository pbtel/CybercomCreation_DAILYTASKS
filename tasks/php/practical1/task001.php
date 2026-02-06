<?php
//variabes
$greeting = "Hello";
$year = 2026;
$count = 2.5;
$isActive = true;

// print using condition 
echo($isActive ? "True" : "False");

// statement with variables
echo $greeting . " World! The year is " . $year;

//used for break line
echo "<br>";

//define and print elements of it and array 
$colours = array("apple", "mango", "banana", "grape");
echo $colours[1];

print_r($colours);

//valiable null 
$discount = null;

//print type of variable using var_dump
var_dump($discount);

?>