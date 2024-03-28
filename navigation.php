<?php
include ("config.inc.php");

if (!empty($host) and !empty($user) and !empty($pass) and !empty($base)) {
    $mysqli = mysqli_connect($host, $user, $pass) or die("Problème de création de la base :" . mysqli_connect_error());

    query($mysqli, "USE $base");

    $aliments = query($mysqli, 'SELECT * FROM `ALIMENT`;');

    // creation d'une liste de tous les aliments avec leur sous-categories
    echo "<ul>";
        while($aliment = mysqli_fetch_assoc($aliments)) {
            $nomAliment = $aliment['nomAliment'];
            echo "<li id='titre'><a href='index.php?p=cocktail&aliment=$nomAliment'>$nomAliment</li><ul>";

            $sousCategories = query($mysqli, 'SELECT * FROM `ALIMENT_SOUS_CATEGORIE` WHERE fiAliment=' . $aliment["idAliment"] . ';');

            while ($sousCategorie = mysqli_fetch_assoc($sousCategories)) {
                $nomSousCategories = query($mysqli, 'SELECT * FROM `ALIMENT` WHERE idAliment=' . $sousCategorie["fiAlimentSousCategorie"] . ';');
                while ($nomSousCategorie = mysqli_fetch_assoc($nomSousCategories)) {
                    $sousCategorieNom = $nomSousCategorie["nomAliment"];
                    echo('<li><a href="index.php?p=cocktail&aliment=' . $sousCategorieNom . '">' . $sousCategorieNom . '</a></li>');
                }
            }
            echo('</ul>');
            }
            echo "</ul>";
        }
?>