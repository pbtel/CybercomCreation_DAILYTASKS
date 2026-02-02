<?php
class pattern{

    public function printpattern($rows,$start){

        if($rows%2 == 0){
            $rows--;
        }

        for($i=0;$i<$rows;$i++){

        if($i < $rows/2){
            //forloop for spacing
            for($k=((($rows-$i-1)/2));$k>0;$k--){
                echo "  ";
            }

            //loop for printing numbers 
            for($j=0;$j<=$i;$j++){
                echo $start . " ";
                $start++;
            }
            echo "<br>";

        }else{
            // for loop for spacing
            for($k=0;$k<($i/2);$k++){
                echo "  ";
            }

            //loop for number 
            for($j=0;$j<($rows-$i);$j++){
                echo $start . "  ";
                $start++;
            }

            echo "<br>";
        
        }
    }
}
}

$pattern1 = new pattern();
$pattern1->printpattern(10,4);
$pattern2 = new pattern();
$pattern2->printpattern(7,2);
?>