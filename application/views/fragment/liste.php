<h2>Liste des images</h2>

<div id="liste-fragment">
</div>

<div class="banderole-creation">
    <?php $this->load->helper('form'); echo form_open_multipart('/fragment/creation/'); ?>
        <div class="file-field input-field inline">
          <div class="btn">
            <span>Votre image</span>
            <input id="imageUpload" type="file" name="image"/>
          </div>
          <div id="nom-fichier" class="file-path-wrapper  col s6">
            <input class="file-path validate" type="text" />
          </div>
        </div>
        <div class="file-field input-field inline">
            <input name="test" type="hidden" />
        </div>

        <button id="bouton-submit" class="btn waves-effect waves-light" type="submit" name="action" onclick="return false;">Envoyer</button>
    </form>
</div>
<script>
    var idUtilisateur = <?php echo $_SESSION["id"] ?>;
</script>
<script src="/js/Fragments/liste.js"></script>