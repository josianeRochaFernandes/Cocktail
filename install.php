<?php
    include "Donnees.inc.php";
    include "config.inc.php";

    // Connexion au serveur MySQL
    if (!empty($host) and !empty($user) and !empty($pass) and !empty($base)) {
        $mysqli = mysqli_connect($host, $user, $pass) or die("Problème de création de la base :" . mysqli_connect_error());

        // Suppression / Création / Sélection de la base de données : $base
        query($mysqli, "DROP DATABASE IF EXISTS $base");

        query($mysqli, "CREATE DATABASE $base");
        mysqli_select_db($mysqli, $base) or die("Problème de chargement de la base");

        query($mysqli, "CREATE TABLE `GESTION_COCKTAILS`.`COCKTAIL` ( `idCocktail` INT NOT NULL , `titre` VARCHAR(90) NOT NULL,`preparation` TEXT NOT NULL , PRIMARY KEY (`idCocktail`)) ENGINE = InnoDB;");

        query($mysqli, "CREATE TABLE `GESTION_COCKTAILS`.`ALIMENT` ( `idAliment` INT NOT NULL , `nomAliment` VARCHAR(40) NOT NULL , `fiSuperCategorie` INT NULL, PRIMARY KEY (`idAliment`) ) ENGINE = InnoDB;");

        query($mysqli, "CREATE TABLE `GESTION_COCKTAILS`.`ALIMENT_SOUS_CATEGORIE` (`fiAliment` INT NOT NULL , `fiAlimentSousCategorie` INT NOT NULL, PRIMARY KEY (`fiAliment`, `fiAlimentSousCategorie`)) ENGINE = InnoDB;");

        query($mysqli, "CREATE TABLE `GESTION_COCKTAILS`.`ALIMENT_APPARTIENT` ( `quantité` VARCHAR(90) NOT NULL , `fiAliment` INT NOT NULL , `fiCocktail` INT NOT NULL , PRIMARY KEY (`fiAliment`, `fiCocktail`)) ENGINE = InnoDB;");

        query($mysqli, "CREATE TABLE `GESTION_COCKTAILS`.`USER` ( `userName` VARCHAR(20) NOT NULL , `prenom` VARCHAR(40) NULL , `password` VARCHAR(12) NOT NULL , `age` INT NULL , PRIMARY KEY (`userName`)) ENGINE = InnoDB;");

        query($mysqli, "CREATE TABLE `GESTION_COCKTAILS`.`COCKTAIL_FAVORITE` ( `fiUser` VARCHAR(20) NOT NULL , `fiCocktail` INT NOT NULL , PRIMARY KEY (`fiUser`, `fiCocktail`)) ENGINE = InnoDB;");

        //query($mysqli, "INSERT INTO `GESTION_COCKTAILS`.`USER`(userName,password) VALUES('root','root');");

        if (!empty($Hierarchie) and !empty($Recettes)) {
            //Ajouter tous les aliments selon cle de l'array $Hierrarchie
            $count=0;
            foreach ($Hierarchie as $aliment => $category) {
                //ajouter aliment sans supercategorie
                query($mysqli, 'INSERT INTO `GESTION_COCKTAILS`.`ALIMENT`(idAliment, nomAliment,fiSuperCategorie) VALUES(' . $count . ',"' . $aliment . '",NULL);');
                $count++;
            }

            //changer fiSupercategorie
            foreach ($Hierarchie as $aliment => $category) {
                    $superCategorie = query($mysqli, 'SELECT idAliment FROM `GESTION_COCKTAILS`.`ALIMENT` WHERE nomAliment="'.$category["super-categorie"][0].'";');
                    $superCategorie = mysqli_fetch_assoc($superCategorie);


                    $idAliment=query($mysqli, 'SELECT idAliment FROM `GESTION_COCKTAILS`.`ALIMENT` WHERE nomAliment="'.$aliment.'";');
                    $idAliment = mysqli_fetch_assoc($idAliment);

                    //ajouter super-categorie sauf si l'aliment est de type ALIMENT
                    if(!empty($superCategorie["idAliment"])) {
                        query($mysqli, 'UPDATE `GESTION_COCKTAILS`.`ALIMENT` SET fiSuperCategorie='.$superCategorie["idAliment"].' WHERE idAliment='.$idAliment["idAliment"].';');
                    }

                foreach ($category["sous-categorie"] as $sousCategorie) {
                    $sousCategorieId = query($mysqli, 'SELECT idAliment FROM `GESTION_COCKTAILS`.`ALIMENT` WHERE nomAliment="' . $sousCategorie . '";');
                    $sousCategorieId = mysqli_fetch_assoc($sousCategorieId);

                    $idAliment=query($mysqli, 'SELECT idAliment FROM `GESTION_COCKTAILS`.`ALIMENT` WHERE nomAliment="'.$aliment.'";');
                    $idAliment = mysqli_fetch_assoc($idAliment);

                    $isAlimentSousCategorie = query($mysqli, 'SELECT * FROM `GESTION_COCKTAILS`.`ALIMENT_SOUS_CATEGORIE` WHERE fiAliment=' . $idAliment["idAliment"] . ' AND fiAlimentSousCategorie=' . $sousCategorieId["idAliment"] . ';');
                    if (mysqli_num_rows($isAlimentSousCategorie) == 0) {
                        query($mysqli, 'INSERT INTO `GESTION_COCKTAILS`.`ALIMENT_SOUS_CATEGORIE`(fiAliment, fiAlimentSousCategorie) VALUES(' . $idAliment["idAliment"] . ',"' . $sousCategorieId["idAliment"] . '");');
                    }
                }
            }


            //Ajouter tous les cocktails
            foreach ($Recettes as $indexCocktail => $cocktail) {
                $preparation=str_replace('"',"''",$cocktail['preparation']);

                query($mysqli, 'INSERT INTO `GESTION_COCKTAILS`.`COCKTAIL`(idCocktail, titre, preparation) VALUES('.$indexCocktail.',"'.$cocktail[titre].'","'.$preparation.'");');
                $ingredients=explode("|",$cocktail['ingredients']);
                foreach ($cocktail['index'] as $nbIngredients => $aliment){
                    //ajouter tous respectif aliments du cocktail dans aliment_appartient
                    //rechercher le id du aliment dans bdAliment
                    $idAliment = query($mysqli, 'SELECT idAliment FROM `GESTION_COCKTAILS`.`ALIMENT` WHERE nomAliment="'.$aliment.'";');
                    $idAliment=mysqli_fetch_assoc($idAliment);


                    $isAlimentAssocierCocktail = query($mysqli, 'SELECT * FROM `GESTION_COCKTAILS`.`ALIMENT_APPARTIENT` WHERE fiCocktail=' . $indexCocktail . ' AND fiAliment=' . $idAliment["idAliment"] . ';');
                    if (mysqli_num_rows($isAlimentAssocierCocktail) == 0) {
                        query($mysqli, 'INSERT INTO `GESTION_COCKTAILS`.`ALIMENT_APPARTIENT`(quantité ,fiAliment, fiCocktail) VALUES("' . $ingredients[$nbIngredients] . '",' . $idAliment["idAliment"] . ',' . $indexCocktail . ');');

                    }
                }
            }
        }

        echo "<p class='alert alert-success'>Chargement réussi -> Base de donnée créer</p>";
        mysqli_close($mysqli);
    }
    else{
        echo "<h1>Problème de chargement de la base</h1>";
    }
?>
