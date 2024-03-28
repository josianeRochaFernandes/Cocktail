<?php
        //afficher un cocktail
        if(isset($_GET['id'])) {
                showCocktailsDetails($_GET['id']);
        }
        // afficher tous les cocktails selon aliment choisie
        else if(isset($_GET['aliment'])){
                showCocktailByAliment($_GET['aliment']);
        }
        // afficher le tableau de tous les aliments avec le nombre de cocktail
        else if(isset($_GET['tableau'])){
                getNbCocktails();
        }
        // affiche tous les cocktails
        else{
                showAllCocktails();
        }

?>
