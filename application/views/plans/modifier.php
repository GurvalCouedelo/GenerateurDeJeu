<script src="/js/Jeux/generation.js"></script>
<script>
    <?php
        echo "afficher(" . $nombreFragments . ");";
        echo "var nombreFragments = " . $nombreFragments . ";";
    ?>
</script>

<div id="conteneur">
</div>
<div class="banderole-creation">
    <button id="bouton-creation" class="btn waves-effect waves-light" type="submit" name="action" onclick="return false;">Envoyer</button>
    <button id="bouton-suppression" class="btn waves-effect waves-light red darken-2 right" name="action" onclick="return false;">Passer en mode suppression de cartes</button>
</div>

<div id="modal1" class="modal">
    <div class="modal-content">
        <p>Voulez vous vraiment supprimer la carte?</p>
    </div>
    <div class="modal-footer">
        <a href="#!" id="confirmation-suppression" class="modal-action modal-close waves-effect waves-green btn-flat">Supprimer</a>
        <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat">Ne rien faire</a>
    </div>
</div>

<script src="/js/Plans/modifier.js"></script>
