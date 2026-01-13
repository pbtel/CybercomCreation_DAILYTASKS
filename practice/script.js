function print(){
    let num = "";
    for(let i=1;i<=10;i++){
        num+= "<br>" + i;
    }
    document.getElementById("result1").innerHTML = num;
}

function printeven(){
    let ans="";
    let i=1;
    while(i<20){
        if(i%2==0){
            ans+= "<br>" + i;
        }
        i++;
    }
    document.getElementById("result2").innerHTML = ans;
}

function printodd(){
    let ans="";
    let i=1;
    while(i<20){
        if(i%2!=0){
            ans+= "<br>" + i;
        }
        i++;
    }
    document.getElementById("result2").innerHTML = ans;
}

function table(){
    let ans = "";
    let num = Number(document.getElementById("num1").value);
    let i=1;
    do{
        ans += "<br>" + (num) + " * " + i + " = " + (num)*i;
        i++;
    }while (i<=10);
    document.getElementById("result3").innerHTML = ans;
}

function breakstatement(){
    let num = "";
    let ans = Number(document.getElementById("num2").value);
    for(let i=1;i<=ans;i++){
        if(i==3){
            break;
        }
        num+= "<br> The number is : " + i;
    }
    document.getElementById("result4").innerHTML = num;
}

function labledbreak(){
    let num = "";
    let ans = Number(document.getElementById("num3").value);
        for(let i=1;i<=ans;i++){
        loopno2 : for(let j=1;j<=ans;j++){
            if(j===3){
            break loopno2;
        }
        num+= "<br> The number is : " + j;
    }
    document.getElementById("result5").innerHTML = num;
}
}
function continuestatement(){
    let num = "";
    let ans = Number(document.getElementById("num4").value);
        for(let i=1;i<=ans;i++){
            for(let j=1;j<=ans;j++){
                if(j==3){
                continue;
                }
                num+= "<br> The number is : " + j;
    }
    document.getElementById("result6").innerHTML = num;
}
}

function labledcontinue(){
    let num = "";
    let ans = Number(document.getElementById("num5").value);
    loopno2 :for(let i=1;i<=ans;i++){
            loopno1 : for(let j=1;j<=ans;j++){
                    if(j===2){
                    continue loopno1;
                    }
        num+= "<br> The number is : " + j;
    }
    document.getElementById("result7").innerHTML = num;
}
}

