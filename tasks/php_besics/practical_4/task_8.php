<?php 
 class Employee{

    public $name;
    private $salary;

    public function __construct($name, $salary){
        $this->name = $name;
        $this->salary = $salary;
    }

    public function getSalary(){
        return $this->salary;
    }

    public function setSalary($salary){
       if($salary >= 0){
        $this->salary = $salary;
       }
       else{
        echo "invalid salary! please enter valid salary.<br>";
       }
    }
 }

    $emp1 = new Employee("Pooja Patel", 30000);

    echo "Employee Name: " . $emp1->name . "<br>";
    echo "Employee current Salary is: " . $emp1->getSalary() . "<br>";

    $emp1->setSalary(35000);
    echo "Employee updated Salary is: " . $emp1->getSalary() . "<br>";
?>