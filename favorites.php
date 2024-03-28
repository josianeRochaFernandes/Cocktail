<?php
    // ajouter ou retire les coeurs d'un cocktail
    if (isset($_GET['event']) and isset($_GET['id'])) {
        // ajouter un cocktail au favoris
        if ($_GET['event'] == 'add') {
            addCocktailToFavorites($_GET['id']);
            showAllCocktails();
        } // retire un cocktail des favories
        else if ($_GET['event'] == 'remove') {
            removeCocktailFromFavorites($_GET['id']);
            showAllCocktails();
        }
    }
    // afficher tous les cocktails favories
    else{
        // si pas connecter
        if ($_SESSION['connecte'] == "false") {
            echo "<p class='alert alert-danger'>Vous n'êtes pas connecter! Connectez-vous si vous avez un compte, sinon enregistrez-vous! </p>";
        } else {
            if (!empty($_SESSION['favorites'])) {
                showFavoritesCocktails(); // show cocktails alphabetic
            } else{
                //pas de cocktail favories ajouter
                echo "<p class='alert alert-info'>Vous n'avez pas des cocktails favorites!</p>";
            }
        }
    }