//var suffixeAjax = "http:/" + "/127.0.0.1/";
var suffixeAjax = "https:/" + "/generateur.titann.fr/";

var listePage = document.getElementById("liste-page"),
    creationPage = document.getElementById("creation-page"),
    liste = true;


//    Déclaration de toutes les fonctions


function intervertir(){
    liste = !liste;

    if(liste === true){
        listePage.style.display = "block";
        creationPage.style.display = "none";
    }
    else{
        listePage.style.display = "none";
        creationPage.style.display = "block";
    }
}

function obtenirListeEntrees(){
    var xhr = new XMLHttpRequest();
    xhr.open('POST', suffixeAjax + document.nomEntite + "/obtenir/");
    xhr.send();

    xhr.addEventListener("readystatechange", function(){
        if(xhr.readyState === 4){
            var listeEntreeBDD = JSON.parse(xhr.responseText),
                listeLi = new Array(),
                conteneurListe = document.getElementById("liste-entrees");
            
            

            conteneurListe.innerHTML = "";

            for(var i in listeEntreeBDD){
                listeLi[i] = document.createElement("li");
                listeLi[i].setAttribute("numero", listeEntreeBDD[i].id);

                var lien = document.createElement("a");
                lien.className = "grey-text";
                lien.innerHTML = listeEntreeBDD[i].nom + " ";
                
                if(document.nomEntite === "selections"){
                    lien.href = "/" + document.nomEntite + "/portail/" + listeEntreeBDD[i].id;
                }
                else if(document.nomEntite === "jeux"){
                    lien.href = "/" + document.nomEntite + "/generer/" + listeEntreeBDD[i].id;
                }

                var boutonModification = document.createElement("i");
                boutonModification.className = "grey-text small material-icons";
                boutonModification.innerHTML = "build";

                boutonModification.addEventListener("click", function(e){
                    transformerChamp(e);
                });

                var boutonSuppression = document.createElement("i");
                boutonSuppression.className = "grey-text small material-icons";
                boutonSuppression.innerHTML = "delete_sweep";
                boutonSuppression.setAttribute("click", "ouvrirDialogueSuppression");

                boutonSuppression.addEventListener("click", function(e){
                    ouvrirDialogueSuppression(e);
                });


                listeLi[i].appendChild(lien);
                listeLi[i].appendChild(boutonModification);
                listeLi[i].appendChild(boutonSuppression);
                conteneurListe.appendChild(listeLi[i]);
            }
        }                     
    });     
}

function transformerChamp(e){
    var html = document.getElementById("html"),
        formulaire = document.getElementById("entreeModification"),
        champNumero = document.getElementById("champNumero"),
        ul = e.target.parentNode.parentNode,

        cible = e.target,
        numeroCible = e.target.parentNode.getAttribute("numero"),

        champPremierePhase = e.target.previousElementSibling.parentNode,
        divNouveauChamp = document.createElement("div"),
        labelNouveauChamp = document.createElement("label"),
        nouveauChamp = document.createElement("input"),
        reinitilalisation = false;


    divNouveauChamp.className = "input-field col s2";
    nouveauChamp.type = "text";
    nouveauChamp.name = "nomSelection";
    nouveauChamp.value = e.target.previousElementSibling.innerHTML;

    labelNouveauChamp.innerHTML = "Nom de votre dossier";

    divNouveauChamp.appendChild(labelNouveauChamp);
    divNouveauChamp.appendChild(nouveauChamp);

    ul.replaceChild(divNouveauChamp, e.target.previousElementSibling.parentNode);
    nouveauChamp.focus();

    html.addEventListener("click", function(e){
        if(reinitilalisation === false){
            if(e.target.tagName !== "INPUT" && e.target.tagName !== "I"){
                champNumero.value = numeroCible;

                var xhr = new XMLHttpRequest();
                var formData = new FormData(formulaire);

                formData.append("nomSelection", nouveauChamp.value);
                formData.append("numero", champNumero.value);

                xhr.open('POST', suffixeAjax + document.nomEntite + "/modification/");
                xhr.send(formData);

                champPremierePhase.firstElementChild.innerHTML = nouveauChamp.value + " ";
                ul.replaceChild(champPremierePhase, divNouveauChamp);

                reinitilalisation = true;
            }
        }
    });
}

function ouvrirDialogueSuppression(e){
    document.numeroCible = e.target.parentNode.getAttribute("numero");

    var elem = document.querySelector('.modal');
    var options = {}
    var instance = M.Modal.init(elem, options);
    instance.open();       
}

function supprimerChamp(){
    var xhr = new XMLHttpRequest();
    xhr.open('POST', suffixeAjax + 'suppressions/');
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("table=" + document.nomEntite + "&id=" + document.numeroCible);

    xhr.addEventListener("readystatechange", function(){
        if(xhr.readyState === 4){
            console.log(xhr.responseText);
            initialisation(document.nomEntite);
        }                     
    });
}


//    Fonction necessaire au fonctionnement de toutes les autres


function initialisation(nomEntite){
    document.nomEntite = nomEntite;
    
    var xhr = new XMLHttpRequest();
    xhr.open('POST', suffixeAjax + nomEntite + "/obtenir/");
    xhr.send();
    
    xhr.addEventListener("readystatechange", function(){
        if(xhr.readyState === 4){
            var listeEntreeBDD = JSON.parse(xhr.responseText);
    
            if(listeEntreeBDD.length === 0){
                intervertir();
            }
            else{
                obtenirListeEntrees();
            }
        }
    });
}
    
    
//    Initialisation de la page
    
  
listePage.style.display = "block";
creationPage.style.display = "none";

var blocPage = document.querySelector('#bloc-page'),
    boutonsIntervertir = blocPage.querySelectorAll('div .bouton-intervertir');


for(var i = 0; i < boutonsIntervertir.length; i++){
    boutonsIntervertir[i].addEventListener("click", function(){
        intervertir();                                           
    });
}

var boutonConfirmationSuppression = document.getElementById("confirmation-suppression");

boutonConfirmationSuppression.addEventListener("click", function(){
    supprimerChamp();
});

    

