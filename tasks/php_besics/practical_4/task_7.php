<?php
class Employee {
    
    public $name;
    public $salary;
    
    public function __construct($name, $salary) {
        $this->name = $name;
        $this->salary = $salary;
    }

}

$emp1 = new Employee("Pooja Patel", 35000);

echo "Employee Name: " . $emp1->name . "<br>";
echo "Employee salary: " . $emp1->salary . "<br>";
?>
