//var suffixeAjax = "http:/" + "/127.0.0.1/";
var suffixeAjax = "https:/" + "/generateur.titann.fr/";

var modeSuppression = false;

function enregistrer(listeCartes){
    for(var i in listeCartes){
        var xhr = new XMLHttpRequest();
        xhr.open('POST', suffixeAjax + 'cartes/enregistrer', false);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        var envoyer = "";
        
        for(var j in listeCartes[i]){
            if(j != 0){
                envoyer += "&";
            }

            envoyer += "fragment" + j +  "=" + listeCartes[i][j];
        }
        

        xhr.send(envoyer);
        
        xhr.addEventListener("readystatechange", function(){
            if(xhr.readyState === 4){
                console.log(xhr.responseText);
                console.log(envoyer);
            }
        });
    }
}

function afficher(nombreFragments = null){
    window.nombreFragments = nombreFragments;
    
//    Nettoyage
    
    var listeCarte = document.querySelectorAll(".carte");
    
    for(var i = 0; i < listeCarte.length; i++){
        listeCarte[i].parentNode.removeChild(listeCarte[i]);
    }
    
//    Obtention
    
    var xhr = new XMLHttpRequest();
    
    if(nombreFragments === null){
        xhr.open('POST', suffixeAjax + 'cartes/obtenir');
        xhr.send();
    }
    else{
        xhr.open('POST', suffixeAjax + 'plans/obtenir');
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        
        var envoyer = "nombreFragments=" + nombreFragments;
        xhr.send(envoyer);
    }
    
    
    xhr.addEventListener("readystatechange", function(){
        if(xhr.readyState === 4){
//            Construction
            console.log(xhr.responseText);
            var tableauCartes = JSON.parse(xhr.responseText);
            var conteneur = document.getElementById("conteneur");
            
            var positionCarteX = 1;
            var positionCarteY = 2;
            var k = 0,
                l = -1,
                pageBreak = [];
            
            
            for(var i in tableauCartes){
                if(k % 6 == 0){
                    l += 1;
                    
                    if(k != 0){
                        positionCarteY += 0.85;
                    }
                    
                    pageBreak[l] = document.createElement("div");
                    pageBreak[l].className = "pagebreak";
//                    pageBreak[l].style = "page-break-after: avoid;";
                    conteneur.appendChild(pageBreak[l]);
                }
                
                var carte = document.createElement("div");
                
                carte.className = "carte";
                carte.style.left = positionCarteX + "cm";
                carte.style.top = positionCarteY + "cm";
                carte.setAttribute("numero", tableauCartes[i][0]["planId"]);
                
                if(positionCarteX + 8.5 >= 11){
                    positionCarteX = 1;
                    positionCarteY += 9;
                }
                
                else{
                    positionCarteX += 9.5;
                }
                console.log(k);
                console.log(pageBreak[l]);
                pageBreak[l].appendChild(carte);
                
                for(var j in tableauCartes[i]){
                    var fragment = document.createElement("img");
                    
                    if(nombreFragments === null){
                        fragment.setAttribute("fragmentId", tableauCartes[i][j]["fragmentId"]);
                        fragment.setAttribute("carteId", tableauCartes[i][j]["carteId"]);
                    }
                    else{
                        fragment.setAttribute("id", tableauCartes[i][j]["id"]);
                    }
                    
                    fragment.className = "fragment-affichage";
                    fragment.style.left = tableauCartes[i][j]["positionX"] + "cm";
                    fragment.style.top = tableauCartes[i][j]["positionY"] + "cm";
                    fragment.style.transform = "rotate(0" + tableauCartes[i][j]["rotation"] + "deg)";
                    
                    var largeurImage,
                        hauteurImage;
                    
                    if(nombreFragments === null){
//                        fragment.style.backgroundImage = "url(/uploads/" + idUtilisateur + "/" + tableauCartes[i][j]["nomFichier"] + ")"; 
                        fragment.src = "/uploads/" + idUtilisateur + "/" + tableauCartes[i][j]["nomFichier"]; 
                        
                        largeurImage = tableauCartes[i][j]["largeurDeBase"];
                        hauteurImage = tableauCartes[i][j]["hauteurDeBase"];
                    }
                    else{
//                        fragment.style.backgroundImage = "url(/img/" + "chat.png" + ")"; 
                        fragment.src.backgroundImage = "/img/" + "chat.png"; 
                        
                        largeurImage = 750;
                        hauteurImage = 500;
                    }
                    
                    fragment.style.width = tableauCartes[i][j]["taille"] + "cm";
                    fragment.style.height = (tableauCartes[i][j]["taille"] * 37.795276) / largeurImage  * (hauteurImage / 37.795276) + "cm";
                    
                    carte.appendChild(fragment);
                }
                
                k += 1;
                
            }
            
            conteneur.style.height = (positionCarteY + 8.5) + "cm";
            
            initialisationEditeur();
        }
    });
}


function initialisationEditeur(){
    var storage = {};
    var elements = document.querySelectorAll(".fragment-affichage"),
        elementsLength = elements.length,
        listeCartes = document.getElementsByClassName("carte"),
        body = document.getElementById("body"),
        aRedimensionner = false,
        modifications = false;

                    
//    Fonctions
    
    function changementTaille(t, aggrandissement){
        if(aggrandissement === true){
            var largeurTemp = parseFloat(t.style.width);  
            t.style.width = (parseFloat(t.style.width.replace(/cm/, '')) + 0.2) + "cm";
            var multiplicateur = parseFloat(t.style.width) / largeurTemp;
            t.style.height = (parseFloat(t.style.height.replace(/cm/, '')) * multiplicateur) + "cm";
        }
        else{
            var largeurTemp = parseFloat(t.style.width);  
            t.style.width = (parseFloat(t.style.width.replace(/cm/, '')) - 0.2) + "cm";
            var multiplicateur = parseFloat(t.style.width) / largeurTemp;
            t.style.height = (parseFloat(t.style.height.replace(/cm/, '')) * multiplicateur) + "cm";
        }
    }
    
    function ouvrirDialogueSuppression(carte){
        window.carte = carte;
        
        var elem = document.querySelector('.modal'),
            options = {},
            instance = M.Modal.init(elem, options);
        instance.open(); 
    }
    
    function supprimmerCarte(){
        window.carte.parentNode.removeChild(window.carte);
        
        var xhr = new XMLHttpRequest();

        xhr.open('POST', suffixeAjax + 'suppressions/');
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    
        xhr.send("table=plancartes" + "&id=" + window.carte.getAttribute("numero"));
                    
        xhr.addEventListener("readystatechange", function(){
            if(xhr.readyState === 4){
                console.log(xhr.responseText);
            }
        });
    }

//    Evenements directs (lorsque l'on clique dessus) sur les fragments
    
    for (var i = 0; i < elementsLength; i++) {
        elements[i].addEventListener('mousedown', function(e) {
            if(modeSuppression === false){
                if(parseInt(e.which) === 1 || parseInt(e.which) === 2){
                    var s = storage;
                    s.target = e.target;
                    s.offsetX = e.clientX - s.target.offsetLeft;
                    s.offsetY = e.clientY - s.target.offsetTop;
                }

                if(parseInt(e.which) === 2){
                    body.style.overflow = "hidden";
                    aRedimensionner = true;

                    e.preventDefault();
                }

                if(parseInt(e.which) === 3){
                    storage.target = e.target;
                    changementTaille(e.target, false);
                    modifications = true;
                }
            }
        });
        
        elements[i].addEventListener('dblclick', function(e) {
            if(modeSuppression === false){
                changementTaille(e.target, true);
                modifications = true;
            }
        });

    }
    
    for(var i = 0; i < listeCartes.length; i++){
        listeCartes[i].addEventListener("click", function(e){
            if(modeSuppression === true){
                ouvrirDialogueSuppression(e.target);
            }
        });
    }
    
    if(nombreFragments !== null){
        var confirmationSuppression = document.getElementById("confirmation-suppression");
        
        confirmationSuppression.addEventListener("click", function(){
            supprimmerCarte();
        });
    }

//    Evenements indirects sur les fragments
    
    document.addEventListener('mousemove', function(e) {
        if(modeSuppression === false){
            var target = storage.target;

            if (target) {
                target.style.top = (e.clientY - storage.offsetY) / 37.795276 + 'cm';
                target.style.left = (e.clientX - storage.offsetX) / 37.795276 + 'cm';

                modifications = true;
            }
        }
    });
    
    document.addEventListener('wheel', function(e) {
        if(modeSuppression === false){
            var target = storage.target,
                s = storage;

            if (target) {
                if(aRedimensionner === false){
                    body.style.overflow = "hidden";

                    /(\d+)deg/.exec(target.style.transform);
                    var rotationActuelle = parseInt(RegExp.$1);

                    if(e.deltaY < 0){
                        target.style.transform = "rotate(" + (rotationActuelle + 4) + "deg)";
                    }

                    else{
                        if(rotationActuelle < 4){
                            target.style.transform = "rotate(" + (360 + (rotationActuelle - 4)) + "deg)";
                        }
                        else{
                            target.style.transform = "rotate(" + (rotationActuelle - 4) + "deg)";
                        }
                    }
                }

                else{
                    if(e.deltaY < 0){
                        changementTaille(target, true);
                    }

                    else{
                        changementTaille(target, false);
                    }
                }

                modifications = true;
            }
        }
    });
    
    
//    Désactivation du menu contextuel
    
//    document.addEventListener('contextmenu', function(e) {
//        e.preventDefault();
//    });
    
//    Desinitialisations et enregistrement
    
    document.addEventListener('mouseup', function() {
        if(modifications === true){
            var xhr = new XMLHttpRequest(),
                t = storage.target;
            
            if(window.nombreFragments === null){
                xhr.open('POST', suffixeAjax + 'cartes/modifier');
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                var positionXActuelle = parseFloat(t.style.left.replace(/cm/, '')),
                    positionYActuelle = parseFloat(t.style.top.replace(/cm/, '')),
                    tailleActuelle = t.style.width.replace(/cm/, '');
                
                /(\d+)deg/.exec(t.style.transform);
                var rotationActuelle = parseInt(RegExp.$1);

                var envoyer = "fragmentId=" + t.getAttribute("fragmentId") + "&carteId=" + t.getAttribute("carteId") + "&positionX=" + positionXActuelle + "&positionY=" + positionYActuelle + "&taille=" + tailleActuelle + "&rotation=" + rotationActuelle; 

                xhr.send(envoyer);
            }
            else{
                xhr.open('POST', suffixeAjax + 'plans/modification');
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                var positionXActuelle = parseFloat(t.style.left.replace(/cm/, '')),
                    positionYActuelle = parseFloat(t.style.top.replace(/cm/, '')),
                    tailleActuelle = t.style.width.replace(/cm/, '');
                
                /(\d+)deg/.exec(t.style.transform);
                var rotationActuelle = parseInt(RegExp.$1);

                var envoyer = "id=" + t.getAttribute("id") + "&positionX=" + positionXActuelle + "&positionY=" + positionYActuelle + "&taille=" + tailleActuelle + "&rotation=" + rotationActuelle; 

                xhr.send(envoyer);
                
                xhr.addEventListener("readystatechange", function(){
                    if(xhr.readyState === 4){
                        console.log(tailleActuelle);
                        console.log(xhr.responseText);
                    }
                });
            }
        
            modifications = false;
            body.style.overflow = "visible";
            aRedimensionner = false;
            storage = {};
        }
    });
}