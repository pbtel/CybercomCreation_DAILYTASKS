// FUNCTION WITH RETURN VALUE
function greetUser(name) {
    return "Hello " + name + ", welcome to JavaScript!";
}
document.getElementById("greet").innerHTML = greetUser("Pooja");

// FUNCTION WITH PARAMETERS & RETURN
function addNumbers(a, b) {
    return a + b;
}
document.getElementById("sum").innerHTML =
    "Addition of 10 and 20 is: " + addNumbers(10, 20);

// FOR LOOP
let output = "";
for (let i = 1; i <= 5; i++) {
    output += "Number: " + i + "<br>";
}
document.getElementById("numbers").innerHTML = output;

// REUSABLE FUNCTION WITH LOOP
function printTable(num) {
    let result = "";
    for (let i = 1; i <= 10; i++) {
        result += num + " * " + i + " = " + (num * i) + "<br>";
    }
    return result;
}
document.getElementById("table").innerHTML = printTable(9);

// ARROW FUNCTION
const square = (n) => {
    return n * n;
};
document.getElementById("square").innerHTML =
    "Square of 6 is: " + square(3);