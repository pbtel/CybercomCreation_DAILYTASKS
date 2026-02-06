<?php
//stirng functions in php
 $str = " Hello World  ";
 $trimedStr = trim($str);
 $lowerStr = strtolower($trimedStr);

 $newstr = str_replace("world", "pooja", $lowerStr);

 echo "Final string is : " . $newstr;

// array functions in php
$numbers = [2,4,5,3,6,7];
sort($numbers);
echo "<h3>sorted array is : </h3>";
print_r($numbers);
 if(in_array(5,$numbers)){
    echo "5 is present in array";
    }
    else{
        echo "5 is not present in array";
    }

    array_push($numbers,8);

    $newarr = [12,15,20];
    $mergearr = array_merge($numbers,$newarr);

    print_r($mergearr);
?>