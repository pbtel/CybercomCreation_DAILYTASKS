    function add() {
    let num1 = Number(document.getElementById("num1").value);
    let num2 = Number(document.getElementById("num2").value);
    document.getElementById("result1").innerText = num1+num2;
    }

    function mul() {
     let num1 = Number(document.getElementById("num1").value);
     let num2 = Number(document.getElementById("num2").value);
    document.getElementById("result1").innerText = num1*num2;
    }

    function sub() {
    let num1 = Number(document.getElementById("num1").value);
    let num2 = Number(document.getElementById("num2").value);
    document.getElementById("result1").innerText = num1-num2;
    }

    function divide() {
    let num1 = Number(document.getElementById("num1").value);
    let num2 = Number(document.getElementById("num2").value);
    document.getElementById("result1").innerText = num1/num2;
    }

    function expo() {
    let num1 = Number(document.getElementById("num1").value);
    let num2 = Number(document.getElementById("num2").value);
    document.getElementById("result1").innerText = num1**num2;
    }

    function modulo() {
    let num1 = Number(document.getElementById("num1").value);
    let num2 = Number(document.getElementById("num2").value);
    document.getElementById("result1").innerText = num1%num2;
    }

    function marge(){
        let text1 = document.getElementById("str1").value;
        let text2 = document.getElementById("str2").value;
        document.getElementById("result2").innerText=text1+text2;
    }

    function and(){
    let x = document.getElementById("num3").value;
    let y = document.getElementById("num4").value;;
    document.getElementById("result3").innerText = x && y;
    } 

    function or(){
    let x = document.getElementById("num3").value;
    let y = document.getElementById("num4").value;;
    document.getElementById("result3").innerText = x || y;
    } 

    function not(){
    let x = document.getElementById("num3").value;
    let y = document.getElementById("num4").value;;
    document.getElementById("result3").innerText = x != y;
    } 

    function andassignment(){
    let x = document.getElementById("num5").value;
    let y = x &&= document.getElementById("num6").value;;
    document.getElementById("result4").innerText = y;
    } 

    function orassignment(){
    let x = document.getElementById("num5").value;
    let y = x ||= document.getElementById("num6").value;;
    document.getElementById("result4").innerText = y;
    } 

    function Nullishcoalescingassignment(){
    let x = document.getElementById("num5").value;
    x ??= Number(document.getElementById("num6").value);;
    document.getElementById("result4").innerText = x;
    } 

    function equalval() {
    let num1 = Number(document.getElementById("num7").value);
    let num2 = Number(document.getElementById("num8").value);
    if(num1===num2){
    document.getElementById("result5").innerText = "Equal value and type ";
    }
    else{
    document.getElementById("result5").innerText = "Only Equal value";
    }
   }

    function agecheck(){
    let num1 = document.getElementById("num9").value;
    if(num1>=18){
        document.getElementById("result6").innerText = "you can drive";
    }
    else{
        document.getElementById("result6").innerText = "you can't drive"; 
    }
    }

    function evenodd(){
    let num1 = document.getElementById("num10").value;
    (num1%2==0) ? (document.getElementById("result7").innerText = "Number is even") : (document.getElementById("result7").innerText = "Number is odd");
    }

    function Daycheck(){

    let date = new Date().getDay();
 
    switch (date) {
     case 0:
        day = "Sunday";
        break;
     case 1:
        day = "Monday";
        break;
     case 2:
        day = "Tuesday";
        break;
     case 3:
        day = "Wednesday";
        break;
     case 4:
        day = "Thursday";
    break;
     case 5:
        day = "Friday";
    break;
     case  6:
        day = "Saturday";
    }
    document.getElementById("result8").innerText = day;
    }

    function same(){
    let num1 = document.getElementById("num11").value;
    document.getElementById("result9").innerText = (num1==5);
    }