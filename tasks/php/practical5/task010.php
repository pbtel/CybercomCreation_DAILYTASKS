<?php
class Employee {
    private $name;
    private $role;

    public function __construct($name, $role) {
        $this->name = $name;
        $this->role = $role;
    }

    // Called when print the object
    public function __toString() {
        return json_encode([
            "name" => $this->name,
            "role" => $this->role
        ]);
    }

    // Called when accessing undefined or inaccessible properties
    public function __get($property) {
        return "Property '{$property}' does not exist.";
    }
}

// object creation
$emp = new Employee("Sonal", "Developer");

echo $emp;              // calls __toString method
echo "<br>";
echo $emp->salary;      // Undefined property handled
?>
