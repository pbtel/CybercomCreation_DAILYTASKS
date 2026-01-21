/* ARRAYS */

let array = ["HTML", "CSS", "JavaScript", "React"];
let arrayOutput = document.getElementById("arrayOutput");

function showArray() {
    arrayOutput.innerHTML = array.join(", ");
}

function arrayLength() {
    arrayOutput.innerHTML = "Array Length: " + array.length;
}

function accessElement() {
    arrayOutput.innerHTML = `
First Element: ${array[0]} <br>
Last Element: ${array[array.length - 1]}
`;
}


/*  ARRAY METHODS */

let methodOutput = document.getElementById("methodOutput");

function pushPop() {
    let temp = [...array];
    temp.push("NodeJS");
    temp.pop();
    methodOutput.innerHTML = temp.join(", ");
}

function shiftUnshift() {
    let temp = [...array];
    temp.unshift("Bootstrap");
    temp.shift();
    methodOutput.innerHTML = temp.join(", ");
}

function mapExample() {
    let lengths = array.map(item => item.length);
    methodOutput.innerHTML = "Length of each item: " + lengths.join(", ");
}

function filterExample() {
    let filtered = array.filter(item => item.length > 3);
    methodOutput.innerHTML = "Filtered (>3 chars): " + filtered.join(", ");
}

function reduceExample() {
    let totalChars = array.reduce((sum, item) => sum + item.length, 0);
    methodOutput.innerHTML = "Total Characters: " + totalChars;
}


/*  OBJECTS */

let objectOutput = document.getElementById("objectOutput");

let student = {
    name: "Pooja",
    course: "Computer Engineering",
    marks: 88
};

function showObject() {
    objectOutput.innerHTML = `
Name: ${student.name}<br>
Course: ${student.course}<br>
Marks: ${student.marks}
`;
}

function updateObject() {
    student.marks += 5;
    objectOutput.innerHTML = "Updated Marks: " + student.marks;
}

function objectMethods() {
    let user = {
        name: "Pooja",
        greet() {
            return "Hello, " + this.name;
        }
    };
    objectOutput.innerHTML = user.greet();
}


/*  ITERATION */

let iterationOutput = document.getElementById("iterationOutput");

function forEachDemo() {
    let result = "";
    array.forEach((item, index) => {
        result += index + " : " + item + "<br>";
    });
    iterationOutput.innerHTML = result;
}

function forOfDemo() {
    let result = "";
    for (let item of array) {
        result += item + " ";
    }
    iterationOutput.innerHTML = result;
}

function forInDemo() {
    let result = "";
    for (let key in student) {
        result += key + " : " + student[key] + "<br>";
    }
    iterationOutput.innerHTML = result;
}
