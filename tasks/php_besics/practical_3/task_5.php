<?php 
declare(strict_types=1);

$taxrate = 0.08;

function calculateTotal(float $amount, int $quantity) : float{
    global $taxrate;
    $total = $amount * $quantity;
    $includeTax = $taxrate*$total;

    return $total + $includeTax;
}

 echo "total amount is : " . calculateTotal(500.56,10);

?>