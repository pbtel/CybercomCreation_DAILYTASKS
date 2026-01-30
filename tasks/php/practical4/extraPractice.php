<?php

class Employee {
    public $name;
    private $salary;

    // Constructor
    public function __construct($name, $salary) {
        $this->name = $name;
        $this->salary = $salary;
    }

    // Getter method
    public function getSalary() {
        return "Current Salary: ₹" . $this->salary;
    }

    // Setter: update salary if it is positive
    public function setSalary($amount) {
        if ($amount > 0) {
            $this->salary = $amount;
            echo "Salary updated successfully!<br>";
        } else {
            echo "Invalid salary amount. Must be positive.<br>";
        }
    }

    // info method
    public function showEmployee() {
        echo "Employee Name: " . $this->name . "<br>";
        echo $this->getSalary() . "<br>";
    }
}

// Create object
$emp = new Employee("Rohit", 40000);

// Show details
$emp->showEmployee();
echo "<br>";

// Try set salary
$emp->setSalary(55000);
echo $emp->getSalary() . "<br><br>";

// Try set nagative salary
$emp->setSalary(-2000);

?>

