//Fonction de detection du click sur le meme
function clickMeme(){
    let meme = document.getElementById("meme");
    meme.addEventListener("click",function(){
        ajaxRequest("GET","src/random.php",display);
    });
}

//Mode Blanc
function wMode(){
    let ban = document.getElementById("banner");
    let valid = true;
    ban.addEventListener("click", function (){
        if(valid) {
            document.getElementById("body").style.backgroundColor = "white";
            document.getElementById("foot").style.color = "black";
            ban.src="images/blackbanner.png";
            valid=false;
        } else {
            document.getElementById("body").style.backgroundColor = "black";
            document.getElementById("foot").style.color = "white";
            ban.src="images/banner.png";
            valid=true;
        }
    });
}

//Request
function ajaxRequest(type,url,callback){
    let request = new XMLHttpRequest();
    request.open(type,url);
    request.onload = () => {
        switch(request.status){
            case 200:
            case 201:
                let response = request.responseText;
                console.log(response);
                callback(response);
                break;
            default:
                console.log("test");
        }
    }
    request.send();
}

//Affichage du meme
function display(response){
    document.getElementById('meme').src = "randomshit/"+response;
}

function main(){
    wMode();
    clickMeme();
}

main();