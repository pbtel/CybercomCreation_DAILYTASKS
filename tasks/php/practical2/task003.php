<?php
$a = 10;
$b = 5;

$sum = $a + $b;
$difference = $a - $b;
$product = $a * $b;
$division = $a / $b;

echo "Sum: " . $sum . "<br>";
echo "Difference: " . $difference . "<br>";
echo "Product: " . $product . "<br>";
echo "Division: " . $division . "<br>";

if ($a > $b) {
    echo "a is greater than b";
} else {
    echo "b is greater than or equal to a";
}

$marks = 78;

if ($marks >= 80) {
    echo "Grade: A<br>";
} elseif ($marks >= 60) {
    echo "Grade: B<br>";
} elseif ($marks >= 40) {
    echo "Grade: C<br>";
} else {
    echo "Grade: Fail<br>";
}

// switch case
$day = "Saturday";

switch ($day) {
    case "Saturday":
    case "Sunday":
        echo "Weekend";
        break;

    case "Monday":
    case "Tuesday":
    case "Wednesday":
    case "Thursday":
    case "Friday":
        echo "Weekday";
        break;

    default:
        echo "Invalid day";
}

?>
