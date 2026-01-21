/*  1. JavaScript Introduction */
document.getElementById("intro").innerText =
    "JavaScript is a scripting language used to make web pages interactive and dynamic.";


/* 2. Script Placement */
document.getElementById("scriptPlace").innerText =
    "Java script able to place in different ways like in header in body and in external page";


/*  3. Variables */
let studentName = "Pooja";
const studentAge = 20;
var course = "Computer Engineering";

document.getElementById("variables").innerText =
    "Name: " + studentName +
    ", Age: " + studentAge +
    ", Course: " + course;


/*  4. Data Types */
let numberType = 10;
let stringType = "JavaScript";
let booleanType = true;
let undefinedType;
let objectType = { language: "JS", level: "Beginner" };

document.getElementById("datatypes").innerText =
    "Number: " + numberType + "\n" +
    "String: " + stringType + "\n" +
    "Boolean: " + booleanType + "\n" +
    "Undefined: " + undefinedType + "\n" +
    "Object: " + JSON.stringify(objectType);


/* 5. Console Logging (Deep) */
console.log("Normal log message");
console.info("Information message");
console.warn("Warning message");
console.error("Error message");

console.log("Student Name:", studentName);
console.log("Student Details Object:", objectType);
