        <link rel="stylesheet" href="/css/application.css">
        <link rel="stylesheet" href="/css/layout.css">
        <link rel="stylesheet" href="/css/typo.css">
        <link rel="stylesheet" href="/css/print.css">

        <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css">
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    </head>
    
    <body id="body">
        <div id="container">
            <nav>
                <div class="nav-wrapper">
                    <a href="#" class="brand-logo center">Générateur de jeu</a>
                    <?php 
                        if(isset($_SESSION["id"])){
                            ?>
                                <ul id="nav-mobile" class="left hide-on-med-and-down">
                                    <li><a href="/selections/">Vos dossiers</a></li>

                                    <?php
                                        if(isset($_SESSION["selection"])){
                                            ?>
                                                <li><a href="/fragment/liste/">Galerie</a></li>
                                                <li><a href="/jeux">Jeu final</a></li>
                                            <?php
                                        }
                                    ?>
                                    <?php
                                        if($_SESSION["permission"] === "A"){
                                            ?>
                                                <li><a href="/plans/selectionner/">Plans</a></li>
                                            <?php
                                        }
                            else{
                                var_dump($_SESSION["permission"]);
                            }
                                    ?>
                                </ul>
                                <ul id="nav-mobile" class="right hide-on-med-and-down">
                                    <li><a href="/acceuil/">Se déconnecter</a></li>
                                </ul>
                            <?php
                        }
                    ?>
                </div>
            </nav>
            <div class="bloc-page" id="bloc-page">
        