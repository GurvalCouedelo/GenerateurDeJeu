<h2>Créer un fragment</h2>

<div class="erreur">
    <?php 
        $this->load->library('form_validation');
        if(!empty(validation_errors())){
            echo validation_errors() . "</br>";
        }
    ?>
</div>
    
<?php $this->load->helper('form'); echo form_open_multipart('/fragment/creation/', array("class" => "grand-formulaire")); ?>
    <div class="file-field input-field">
        <div class="btn">
            <span>Votre image</span>
            <input type="file" name="image"/>
        </div>
        <div class="file-path-wrapper">
        <input class="file-path validate" type="text" />
        </div>
    </div>

    <button class="btn waves-effect waves-light" type="submit" name="action">Envoyer</button>
</form>