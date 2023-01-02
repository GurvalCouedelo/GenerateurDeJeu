//var suffixeAjax = "http:/" + "/127.0.0.1/";
var suffixeAjax = "https:/" + "/generateur.titann.fr/";

var listeFragments = document.getElementById("liste-fragment");

function chargerListe(){
    var xhr = new XMLHttpRequest();
    xhr.open('POST', suffixeAjax + 'fragment/obtenir/');
    xhr.send();
    
    xhr.addEventListener("readystatechange", function(){
        if(xhr.readyState === 4){
            var reponse = JSON.parse(xhr.responseText);
            var tableauFragmentListeDiv = new Array();
            var tableauFragmentListeCase = new Array();
            var tableauFragmentListeLabel = new Array();
            var tableauFragmentListeCaseSpan = new Array();
            
            listeFragments.innerHTML = "";
            
            for (var i in reponse["listeFragments"]) {
                tableauFragmentListeDiv[i] = document.createElement("div");
                tableauFragmentListeDiv[i].className = "fragment-liste-div";
                tableauFragmentListeDiv[i].style.backgroundImage = "url('/uploads/" + idUtilisateur + "/" + reponse["listeFragments"][i].nomFichier + "')";
                tableauFragmentListeDiv[i].setAttribute("numero", reponse["listeFragments"][i].id);
                
                tableauFragmentListeDiv[i].addEventListener("dblclick", function(e){
                    var xhr = new XMLHttpRequest();

                    xhr.open('POST', suffixeAjax + 'suppressions/');
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    
                    xhr.send('id=' + e.target.getAttribute("numero") + "&table=fragments");
                    
                    xhr.addEventListener("readystatechange", function(){
                        if(xhr.readyState === 4){
                            console.log(xhr.responseText);
                            chargerListe();
                        }
                    });
                    
                });
                
//                Création de la case elle-même
                
                tableauFragmentListeCase[i] = document.createElement("input");
                tableauFragmentListeCase[i].className = "filled-in";
                tableauFragmentListeCase[i].type = "checkbox";
                tableauFragmentListeCase[i].id = "case-a-cocher-" + reponse["listeFragments"][i].id;
                tableauFragmentListeCase[i].setAttribute("numero", reponse["listeFragments"][i].id);
                
                tableauFragmentListeCaseSpan[i] = document.createElement("span");
                
//                Etat par défaut de la case
                
                
                if(reponse["listeFragments"][i].selectionne == 1){
                    tableauFragmentListeCase[i].setAttribute("checked", "checked");
                }
                    
                
//                Evenement de la case à cocher
                
                tableauFragmentListeCase[i].addEventListener("change", function(e){
                    var xhr = new XMLHttpRequest();

                    xhr.open('POST', suffixeAjax + 'fragment/selectionner/');
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    
                    xhr.send('numero=' + e.target.getAttribute("numero") + '&selectionne=' + e.target.checked);
                    console.log(e.target.getAttribute("numero"));
                });
                
                tableauFragmentListeLabel[i] = document.createElement("label");
                tableauFragmentListeLabel[i].className = "case-a-cocher-fragmentheque";
                
                tableauFragmentListeLabel[i].addEventListener("dblclick", function(e){
                    e.stopPropagation();
                });
                
//                Assemblage
                
                tableauFragmentListeLabel[i].appendChild(tableauFragmentListeCase[i]);
                tableauFragmentListeLabel[i].appendChild(tableauFragmentListeCaseSpan[i]);
                tableauFragmentListeDiv[i].appendChild(tableauFragmentListeLabel[i]);
                listeFragments.appendChild(tableauFragmentListeDiv[i]);
            }
            
            
            delete xhr;
        }
    })
}

var boutonSubmit = document.getElementById("bouton-submit");

boutonSubmit.addEventListener("click", function(ev){
    var champUpload = document.getElementById("imageUpload"),
        formulaire = document.querySelector("form"),
        xhr = new XMLHttpRequest();
    
    var formData = new FormData(formulaire);
    formData.append('image', champUpload.files[0]);
    
    if(/image/i.test(champUpload.files[0].type)){
        xhr.open('POST', suffixeAjax + 'fragment/creation/');
    }
    else if(/application\/x-zip-compressed/i.test(champUpload.files[0].type)){
        xhr.open('POST', suffixeAjax + 'fragment/uploadZip/');
    }
    
    xhr.send(formData);
    
    xhr.addEventListener("readystatechange", function(){
        if(xhr.readyState === 4){
            console.log(xhr.responseText);
            chargerListe();
        }
    });
    
    ev.preventDefault();
});

chargerListe();
