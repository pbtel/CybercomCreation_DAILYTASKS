<?php 
 class Employee{
    //variables
    public $name;
    private $salary;

    //constructor for employee
    public function __construct($name, $salary){
        $this->name = $name;
        $this->salary = $salary;
    }

    //get salary
    public function getSalary(){
        return $this->salary;
    }

    //set salary
    public function setSalary($salary){
       if($salary >= 0){
        $this->salary = $salary;
       }
       else{
        echo "invalid salary! please enter valid salary.<br>";
       }
    }
 }

    //create object
    $emp1 = new Employee("Pooja Patel", 30000);

    //print details
    echo "Employee Name: " . $emp1->name . "<br>";
    echo "Employee current Salary is: " . $emp1->getSalary() . "<br>";

    $emp1->setSalary(35000);
    echo "Employee updated Salary is: " . $emp1->getSalary() . "<br>";
?>