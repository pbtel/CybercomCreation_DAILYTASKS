<?php
class myclass{

    public function printpattern($rows,$start){

        for($i=0;$i<$rows;$i++){
            for($j=0;$j<=$i;$j++){
                echo "$start ";
                $start++;
            }
            echo "<br>";
        }
    }
}

//create object for first pattern
$pattern1 = new myclass();
$pattern1->printpattern(5,1);

// create object for second pattern
$pattern2 = new myclass();
$pattern2->printpattern(4,10);
?>