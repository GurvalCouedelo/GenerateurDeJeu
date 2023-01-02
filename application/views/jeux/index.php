<div class="erreur">
    <?php
        if(isset($_SESSION["erreurs"])){
            foreach($_SESSION["erreurs"] as $erreurBoucle){
                echo $erreurBoucle;
            }
                
            unset($_SESSION["erreurs"]);
        }
    ?>
</div>

<div id="liste-page" class="elements-page" display="block">
    <h2>Vos jeux</h2>
    <?php $this->load->helper('form'); echo form_open_multipart('/jeux/modification', array("id" => "entreeModification")); ?>
        <ul id="liste-entrees">
        </ul>
        <input type="hidden" name="numero" id="champNumero"/>
    </form>
    <button class="btn waves-effect waves-light bouton-intervertir">Créer un jeu</button>
</div>

<div id="creation-page" class="elements-page" display="none">
    <h2>Créer un jeu</h2>
    <div class="erreur">
        <?php 
            $this->load->library('form_validation');
            echo validation_errors(); 
        ?>
    </div>
    
    <?php $this->load->helper('form'); echo form_open_multipart('/jeux', array("class" => "grand-formulaire")); ?>
        <div class="input-field col s12">
            <label for="nom">Nom du jeu:</label>
            <input id="nom" type="text" name="nom" />
        </div>

        <button id="bouton-submit" class="btn waves-effect waves-light" type="submit" name="action">Envoyer</button>
    </form>
    <button class="btn waves-effect waves-light bouton-intervertir">Choisir un jeu</button>
</div>

<div id="modal1" class="modal">
    <div class="modal-content">
        <p>Voulez vous vraiment supprimer le jeu?</p>
    </div>
    <div class="modal-footer">
        <a href="#!" id="confirmation-suppression" class="modal-action modal-close waves-effect waves-green btn-flat">Supprimer</a>
        <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat">Ne rien faire</a>
    </div>
</div>

<script src="/js/Listes/index.js"></script>

<script>
    initialisation("jeux");
</script>