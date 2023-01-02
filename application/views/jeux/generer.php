<h2>Génèrer un jeu</h2>

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


<?php $this->load->helper('form'); echo form_open_multipart("/jeux/generer/" . $idJeu, array("class" => "grand-formulaire")); ?>
    <div class="input-field col s6">
        <select name="nombreFragments">
            <option value="" disabled selected>Choisissez le nombre de fragments par cartes</option>
            <?php 
                foreach($possibilites as $possibiliteCleBoucle => $possibiliteBoucle){
                    echo "<option value=\"" . $possibiliteCleBoucle . "\">" . $possibiliteCleBoucle . " fragments par carte</option>";
                }
            ?>
        </select>
        <button id="bouton-submit" class="btn waves-effect waves-light" type="submit" name="action">Envoyer</button>
    </div>
</form>

<script>
    $(document).ready(function(){
        $('select').formSelect();
      });
</script>
