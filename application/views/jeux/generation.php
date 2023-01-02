<script>
    var idUtilisateur = <?php echo $_SESSION["id"] ?>;
</script>
<script src="/js/Jeux/generation.js"></script>

<div id="conteneur"></div>

<div class="banderole-creation">
    <button class="btn waves-effect waves-light" onclick="window.print()"><i class="material-icons left">local_printshop</i>Imprimer</button>
</div>

<script>
    <?php
        if($cartes !== null){
            echo  "enregistrer(" . json_encode($cartes) . ");";
        }
    ?>
    afficher();
</script>