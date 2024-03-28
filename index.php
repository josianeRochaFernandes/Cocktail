<?php
    session_start();
    if(!isset($_SESSION["connecte"]))
    {
        $_SESSION["connecte"] = "false";
    }
    include "Fonctions.inc.php";
    include "config.inc.php";

?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8"/>

        <title>Cocktails</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="style.css" type="text/css">


        <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>

        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

        <script>
            $(document).ready(function() {
                // ajouter les cocktails aux favorites en cliquant sur le coeur
                $('.coeur').click(function () {
                    if ($(this).attr('src') == "Photos/coeur_vide.png") {
                        $(this).attr('src', "Photos/coeur_full.png");

                    } else {
                        $(this).attr('src', "Photos/coeur_vide.png");

                    }
                });

            });
        </script>
		
	</head>
	<body>
		<header>
            <nav class="navbar navbar-expand-sm bg-light navbar-light">
                <a class="navbar-brand" href="index.php">Navigation</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="collapsibleNavbar">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" id="bd" href="index.php?p=install">Création de base de données</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?p=favorites">Recettes <i class="fa fa-heart"></i> </a>
                        </li>

                        <?php

                        if($_SESSION["connecte"] == "true"){
                            echo "<li class='zoneConnexion'>
                                    <label id='username'>
                                        Bonjour $_SESSION[login]
                                    </label>
                                    <button id='tableau' type='button' class='btn btn-primary'>
                                        <a href = 'index.php?p=cocktail&tableau=y'>Tableau</a>
                                    </button>
                                    <button id='profil' type='button' class='btn btn-primary'>
                                        <a href='index.php?p=profil'>Profil</a>
                                     </button>
                                    <button id='user_out' type='button' class='btn btn-primary' name='Deconnexion' >
                                    <a href='index.php?p=logout'>Se déconnecter</a>
                                    </button>
                                  </li>";
                        }
                        else {
                            echo "<li class='zoneConnexion'>
                                    <form class='form-inline' action='index.php?p=login' method='post'>
                                        <label for='login' class='mr-sm-1'>Login:</label>
                                        <input type='text' class='form-control mb-1' placeholder='Entrer username' id='login' name='login' >
                                        <label for='pwd' class='mr-sm-1'>Password:</label>
                                        <input type='password' class='form-control mb-1' placeholder='Entrer password' id='pwd' name='pwd' >
                                        <button type='submit' name='submit' value='Valider' class='btn btn-primary' id='submit' >
                                            <a>Connexion</a>
                                        </button>
                                        <button type='button' class='btn btn-primary' name='inscription' id='user_inscription'>
                                            <a href='?p=inscription'>S'inscrire</a>
                                        </button>
                                    </form>
                                  </li>";
                        }
                        ?>

                    </ul>
                </div>
            </nav>
        </header>

        <aside>
            <?php include("navigation.php") ?>
        </aside>

		<section>
                    <?php

                        //print_r($_SESSION);
                        //rechercher les pages associer à p
                        if (!isset($_GET['p'])) {
                            $_GET['p'] = 'cocktail';
                        }

                        if (isset($_GET['p'])) {
                            $fichier = $_GET['p'] . '.php';

                        }

                        if (file_exists($fichier)) {
                            include($fichier);
                        } else {
                            echo "Erreur 404 : la page demandée n’existe pas";
                        }


                    ?>


		</section>
        
	</body>
</html>