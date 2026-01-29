<?php 

echo "<h3> multiplication table of 5 using for loop</h3>";

for($i=0;$i<=10;$i++){

echo "5 x $i = " . (5*$i) . "<br>";
}


echo "<h3> even numbers between 1 to 20 using while loop</h3>";

$i=1;
while($i<20){

    if($i%2==0){
        echo $i . "<br>";
    }
    $i++;
}

echo "<h3>for each loop to iterate through an array</h3>";
$arr = ["name" => "pooja" ,
        "age" => 21,
        "city" => "mehsana",
        "subjects" => ["html","css","js"]
        ];

foreach($arr as $key => $value){
    if(is_array($value)){
        echo $key . " : " . implode("," , $value) . "<br>";
    }
    else{
        echo $key . " : " . $value . "<br>";
    }
}

?>
