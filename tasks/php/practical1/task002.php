<?php

//define array
$person = [
    "name" => "Pooja Patel",
    "age" => 21,
    "skills" => ["PHP", "JavaScript", "MySQL"]
];

//print output in formate using <pre> tag 
echo "<h3>Output is :</h3>";
echo "<pre>";
print_r($person);
echo "</pre>";

//print array data types in format 
echo "<h3>use var_dump to show data types and length</h3>";
echo "<pre>";
var_dump($person);
echo "</pre>";
?>
