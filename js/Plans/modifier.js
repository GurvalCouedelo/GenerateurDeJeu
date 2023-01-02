//var suffixeAjax = "http:/" + "/127.0.0.1/",
var suffixeAjax = "https:/" + "/generateur.titann.fr/",
    boutonCreation = document.getElementById("bouton-creation"),
    boutonSuppression = document.getElementById("bouton-suppression"),
    conteneur = document.querySelector("#conteneur");

// Interface

boutonCreation.addEventListener("click", function(){
    var xhr = new XMLHttpRequest();
    xhr.open('POST', suffixeAjax + 'plans/creation');
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("nombreFragments=" + nombreFragments);
    
    xhr.addEventListener("readystatechange", function(){
        if(xhr.readyState === 4){
            afficher(nombreFragments);
        }                     
    });
});

boutonSuppression.addEventListener("click", function(){
    var cartes = document.getElementsByClassName("carte");
    modeSuppression = !modeSuppression;
    
    if(modeSuppression === true){
        boutonSuppression.innerHTML = "Passer en mode modification de fragments";
        boutonSuppression.className = boutonSuppression.className.replace(/red darken-2/, "green lighten-1");
        
        for(var i = 0; i < cartes.length; i++){
            cartes[i].className += " mode-suppression";
        }
        
    }
    else{
        boutonSuppression.innerHTML = "Passer en mode suppression de cartes";
        boutonSuppression.className = boutonSuppression.className.replace(/green lighten-1/, "red darken-2");
        
        for(var i = 0; i < cartes.length; i++){
            cartes[i].className = cartes[i].className.replace(/ mode-suppression/, '');
        }
    }
});




