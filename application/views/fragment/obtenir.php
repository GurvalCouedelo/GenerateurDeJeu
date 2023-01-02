{listeFragments}
    <img class="fragment-liste" src="/uploads/{nomFichier}"/>
{/listeFragments}

<?php
    if(empty($listeFragments))
    {
        ?>
        <p>Vous n'avez pas encore de fragments.
        <?php
    }
?>