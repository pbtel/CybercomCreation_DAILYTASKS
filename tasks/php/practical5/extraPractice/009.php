<?php
// Parent class
class Vehicle {
    protected $brand;

    public function __construct($brand) {
        $this->brand = $brand;
    }

    // Method to be overridden
    public function getDetails() {
        return "Vehicle Brand: " . $this->brand;
    }
}

// Child class
class Car extends Vehicle {
    public $fuelType;

    public function __construct($brand, $fuelType) {
        parent::__construct($brand);
        $this->fuelType = $fuelType;
    }

    // Overriding parent method
    public function getDetails() {
        return parent::getDetails() . 
               "and Fuel Type: " . $this->fuelType;
    }

    // Child-specific method
    public function drive() {
        return "The " . $this->brand . " car runs on " . $this->fuelType . ".";
    }
}

// Create object
$car = new Car("Toyota", "Petrol");

// Output
echo $car->getDetails() . "<br>";
echo $car->drive();
?>
