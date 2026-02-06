<?php
declare(strict_types=1);

function showMessage(): void {
    $msg = "This is a local variable";
    echo $msg;
}

showMessage();
// echo $msg

//use of diffrent array function
$numbers = [1, 3, 7, 9];

if (in_array(7, $numbers)) {
    echo "7 found in array<br>";
}

array_push($numbers, 11);

$extraNumbers = [13, 15];
$mergedArray = array_merge($numbers, $extraNumbers);

print_r($mergedArray);

//use of stings inbuilt functions 

$text = "   PHP Is POWERFUL   ";

$text = trim($text);
$text = strtoupper($text);
$text = str_replace("POWERFUL", "AWESOME", $text);

echo $text;

//function call with using strict_types
function calculateBill(float $amount, int $discountPercent): float {
    $discount = ($amount * $discountPercent) / 100;
    return $amount - $discount;
}

echo calculateBill(1200.50, 10);

?>
