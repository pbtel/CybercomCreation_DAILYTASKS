<?php

$person = [
    "name" => "Pooja Patel",
    "age" => 21,
    "skills" => ["PHP", "JavaScript", "MySQL"]
];

echo "<h3>Output is :</h3>";
echo "<pre>";
print_r($person);
echo "</pre>";

echo "<h3>use var_dump to show data types and length</h3>";
echo "<pre>";
var_dump($person);
echo "</pre>";
?>
