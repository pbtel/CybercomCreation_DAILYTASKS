<?php 
    // define variable
    $num = 12;

    //diffrence between " " and ' '
    echo "the number is $num";
    echo 'the num is $num';

    // variable string
    $x = "Hello World!";

    //lenth of string
    echo strlen($x);

    //check for contain word or not 
    var_dump(str_contains($x,"india"));
    var_dump(str_contains($x,"World"));

    //count the number of words 
    echo str_word_count($x);

    //used for reverse string
    echo strrev($x);

    // print in upper or lower case 
    echo strtolower($x);
    echo strtoupper($x);


    //print array with id number 


    $subjects = ["HTML", "CSS", "JavaScript", "PHP"];
    $count = 1;

    foreach ($subjects as $sub) {
        echo $count . ". " . $sub . "<br>";
        $count++;
    }


    //marks wise check pass or fail 
    $student = ["maths" =>50 ,"physics" => 40, "chemistry" => 18, "biology" => 40];

    foreach($student as $subject => $marks){
        if($marks>18){
            echo "pass in subject - " . $subject; 
        }
        else{
            echo "fail in subject - " . $subject;
        }
    }

?>