<?php
class Employee {
    //variable declared
    public $name;
    public $salary;
    
    // cunsructor for initialize values
    public function __construct($name, $salary) {
        $this->name = $name;
        $this->salary = $salary;
    }

}

//create object
$emp1 = new Employee("Pooja Patel", 35000);

//print details
echo "Employee Name: " . $emp1->name . "<br>";
echo "Employee salary: " . $emp1->salary . "<br>";
?>
