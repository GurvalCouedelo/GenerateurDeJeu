<div class="erreur">
    <?php 
        foreach($_SESSION["erreurs"] as $erreur){
            if(!empty($erreur)){
                echo $erreur . "</br>";
            }
        }
    ?>
</div>

<?php $this->load->helper('form'); echo form_open('/', array("class" => "grand-formulaire")); ?>
    <div class="input-field col s12">
        <label for="pseudo">Votre pseudo:</label>
        <input id="pseudo" type="text" name="pseudo" />
    </div>

    <div class="input-field col s12">
        <label for="passe">Votre mot de passe:</label>
        <input id="passe" type="password" name="passe" />
    </div>

    
    <button class="btn waves-effect waves-light" type="submit" name="action">Envoyer</button>
</form>