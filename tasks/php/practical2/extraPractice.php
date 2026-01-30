<?php

// use of if....elseif 
$percentage = 68;

if ($percentage >= 75) {
    echo "Scholarship Approved";
} elseif ($percentage >= 60) {
    echo "Partial Scholarship";
} elseif ($percentage >= 40) {
    echo "No Scholarship";
} else {
    echo "Not Eligible";
}

//switch case 

$signal = "Yellow";

switch ($signal) {
    case "Red":
        echo "Stop";
        break;
    case "Yellow":
        echo "Get Ready";
        break;
    case "Green":
        echo "Go";
        break;
    default:
        echo "Invalid Signal";
}

//foreach loop for show student is absent or present 

$attendance = [
    "Amit" => "Present",
    "Neha" => "Absent",
    "Ravi" => "Present"
];

foreach ($attendance as $name => $status) {
    echo "$name is $status <br>";
}

?>
