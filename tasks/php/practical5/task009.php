<?php
// Parent class
class Employee {
    public $name;
    protected $salary;

    public function __construct($name, $salary) {
        $this->name = $name;
        $this->salary = $salary;
    }

    // Method to be overridden
    public function getDetails() {
        return "Employee Name: " . $this->name;
    }
}

// Child class
class Manager extends Employee {
    public $department;

    public function __construct($name, $salary, $department) {
        parent::__construct($name, $salary);
        $this->department = $department;
    }

    // Overriding parent method
    public function getDetails() {
        return parent::getDetails() . 
               " & Department: " . $this->department;
    }

    // Method similar to your snippet
    public function report() {
        return $this->name . " manages the " . $this->department . " department.";
    }
}

// Create object of Manager
$mgr = new Manager("Anita", 80000, "IT");

// Output
echo $mgr->getDetails() . "<br>";
echo $mgr->report();
?>
