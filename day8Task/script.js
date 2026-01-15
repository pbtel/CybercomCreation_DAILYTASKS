/* FUNCTIONS SECTION */

let functionOutput = document.getElementById("functionOutput");

function greetUser(name) {
    return `Hello ${name}, welcome to JavaScript!`;
}

function calculateDiscount(price, discount = 10) {
    return price - (price * discount / 100);
}

function isEven(num) {
    return num % 2 === 0;
}

function multiplier(factor) {
    return function (number) {
        return number * factor;
    };
}

function calculateSum(arr) {
    let sum = 0;
    for (let n of arr) sum += n;
    return sum;
}

function showGreet() {
    functionOutput.innerHTML = greetUser("Pooja");
}

function showDiscount() {
    functionOutput.innerHTML = `
Price ₹1000 <br>
Default Discount: ₹${calculateDiscount(1000)} <br>
20% Discount: ₹${calculateDiscount(1000, 20)}
`;
}

function checkEven() {
    functionOutput.innerHTML = `
8 is Even: ${isEven(8)} <br>
7 is Even: ${isEven(7)}
`;
}

function showReusable() {
    let double = multiplier(2);
    let triple = multiplier(3);

    functionOutput.innerHTML = `
Double of 5: ${double(5)} <br>
Triple of 5: ${triple(5)}
`;
}

function arraySum() {
    functionOutput.innerHTML =
        `Sum of [10,20,30,40] = ${calculateSum([10,20,30,40])}`;
}


/*  LOOPS SECTION  */

let loopOutput = document.getElementById("loopOutput");

function forLoopDemo() {
    let result = "";
    for (let i = 1; i <= 10; i++) result += i + " ";
    loopOutput.innerHTML = result;
}

function evenLoopDemo() {
    let result = "";
    for (let i = 1; i <= 20; i++) {
        if (i % 2 === 0) result += i + " ";
    }
    loopOutput.innerHTML = result;
}

function whileLoopDemo() {
    let i = 5, result = "";
    while (i >= 1) {
        result += i + " ";
        i--;
    }
    loopOutput.innerHTML = result;
}

function doWhileDemo() {
    let x = 1, result = "";
    do {
        result += x + " ";
        x++;
    } while (x <= 5);
    loopOutput.innerHTML = result;
}

function nestedLoopDemo() {
    let pattern = "";
    for (let r = 1; r <= 4; r++) {
        for (let c = 1; c <= r; c++) {
            pattern += "* ";
        }
        pattern += "<br>";
    }
    loopOutput.innerHTML = pattern;
}


/*  ARROW FUNCTIONS SECTION  */

let arrowOutput = document.getElementById("arrowOutput");

const welcome = () => "Welcome to Arrow Functions";

const square = num => num * num;

const add = (a, b) => a + b;

const createStudent = (name, marks) => ({
    name,
    marks,
    result: marks >= 40 ? "Pass" : "Fail"
});

const calculateAverage = numbers => {
    let sum = 0;
    for (let n of numbers) sum += n;
    return sum / numbers.length;
};

function arrowWelcome() {
    arrowOutput.innerHTML = welcome();
}

function arrowMath() {
    arrowOutput.innerHTML = `
Square of 6: ${square(6)} <br>
15 + 25 = ${add(15, 25)}
`;
}

function studentResult() {
    let student = createStudent("Pooja", 85);
    arrowOutput.innerHTML = `
Name: ${student.name}<br>
Marks: ${student.marks}<br>
Result: ${student.result}
`;
}

function averageCalc() {
    arrowOutput.innerHTML =
        `Average of [70,80,90] = ${calculateAverage([70,80,90])}`;
}
