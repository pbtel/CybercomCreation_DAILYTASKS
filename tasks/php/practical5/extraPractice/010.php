<?php
class Product {
    private $data = [];

    // used when setting undefined or private property
    public function __set($name, $value) {
        if ($value !== null) {
            $this->data[$name] = $value;
            echo "Property '{$name}' set to '{$value}'<br>";
        } else {
            echo "Cannot set null value<br>";
        }
    }

    // used to check if property exists with isset()
    public function __isset($name) {
        return isset($this->data[$name]);
    }

    // Display all product info
    public function showProduct() {
        return json_encode($this->data);
    }
}

// Usage
$product = new Product();

// Setting properties that don't exist
$product->name = "Laptop";       
$product->price = 55000;         

// Check if property exists
if (isset($product->price)) {    
    echo "Price is set.<br>";
}

// Display all data
echo $product->showProduct();
?>
