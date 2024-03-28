<?php

  function query($link,$query)
  { 
    $resultat=mysqli_query($link,$query) or die("$query : ".mysqli_error($link));
	return($resultat);
  }

    //retourne désignation d'un title comme array
    function getDesignation($title){
        $result=[];
        $title = strtolower($title);
        $title = ucfirst($title);

        $words = explode(" ", $title);
        foreach ($words as $index => $word){
            if($word[0]==":" or $word[0]=="("){
                break;
            }
            $result[]=$word;
        }
        return $result;
    }

    //retourne nom de tous les images dans dossier Photos
    function getAllImageNames(){
        $dirImages = opendir('Photos');
        $imageArray=[];
        while (false !== ($entry = readdir($dirImages))) {
            $imageArray[]=$entry;
        }
        return $imageArray;
    }

    //retourne nom de l'image selon le désignation du titre
    function getImageByTitle($titleDesignation){
        $images = getAllImageNames();
        $title = implode("_",$titleDesignation).'.jpg';
        $result='cocktail.png';
        foreach ($images as $index => $image){
            if($image==$title){
                $result=$image;
                break;
            }
        }
        return $result;
    }

    //ajoute un cocktail dans la session['favorites']
    function addCocktailToFavorites($idCocktail){
        include ("config.inc.php");
        if (!empty($host) and !empty($user) and !empty($pass) and !empty($base)) {
            $mysqli = mysqli_connect($host, $user, $pass) or die("Problème de création de la base :" . mysqli_connect_error());

            query($mysqli, "USE $base");

            if (isset($_SESSION['connecte']) && $_SESSION['connecte'] == "true") {
                // utilisateur connecté
                $user = $_SESSION['login'];

                // ajoute l'id du cocktail dans la base de données avec l'id de l'utilisateur connecté
                query($mysqli, 'INSERT INTO COCKTAIL_FAVORITE(fiUser, fiCocktail) VALUES ("'.$user.'",'.$idCocktail.');');

                // enregistrer le cocktails dans la session
                $cocktails = query($mysqli, 'SELECT * FROM COCKTAIL WHERE idCocktail='.$idCocktail.';');
                while($cocktail= mysqli_fetch_assoc($cocktails)){
                    $_SESSION['favorites'][$idCocktail]=$cocktail['titre'];
                    ksort($_SESSION['favorites'], SORT_NUMERIC);
                }

            }
            mysqli_close($mysqli);
        }
    }

    //elimine un cocktail dans la session['favorites']
    function removeCocktailFromFavorites($idCocktail){
        include ("config.inc.php");
        if (!empty($host) and !empty($user) and !empty($pass) and !empty($base)) {
            $mysqli = mysqli_connect($host, $user, $pass) or die("Problème de création de la base :" . mysqli_connect_error());

            query($mysqli, "USE $base");

            if (isset($_SESSION['connecte']) && $_SESSION['connecte'] == "true") {
                // utilisateur connecté
                $user = $_SESSION['login'];

                // retire l'id du cocktail dans la base de données de l'utilisateur connecté
                query($mysqli, 'DELETE FROM COCKTAIL_FAVORITE WHERE fiCocktail='.$idCocktail.' AND fiUser="'.$user.'";');
                // retire cocktail de la session
                unset($_SESSION['favorites'][$idCocktail]);
            }
            mysqli_close($mysqli);
        }
    }

    //return array de tous les cocktail selon la categorie en question
    function getNbCocktails()
    {
        include ("config.inc.php");
        $array = array();

        if (!empty($host) and !empty($user) and !empty($pass) and !empty($base)) {
            $mysqli = mysqli_connect($host, $user, $pass) or die("Problème de création de la base :" . mysqli_connect_error());

            query($mysqli, "USE $base");

            echo "<table class='table table-striped table-bordered table-sm display data-table sortable' id='tab'>
                <thead>
               <tr>";

            if(isset($_GET['order'])){
                if ($_GET['order'] == 'ASC') {
                    echo "<th><a href='index.php?p=cocktail&tableau=y&sort=recette&order=DESC'>Nombre de recettes</a></th>
                        <th><a href='index.php?p=cocktail&tableau=y&sort=aliment&order=DESC'>Aliment</th>";
                } else if($_GET['order'] == 'DESC'){
                    echo "<th><a href='index.php?p=cocktail&tableau=y&sort=recette&order=ASC'>Nombre de recettes</a></th>
                        <th><a href='index.php?p=cocktail&tableau=y&sort=aliment&order=ASC'>Aliment</th>";
                }
            }
            else{
                echo "<th><a href='index.php?p=cocktail&tableau=y&sort=recette&order=DESC'>Nombre de recettes</a></th>
                       <th><a href='index.php?p=cocktail&tableau=y&sort=aliment&order=DESC'>Aliment</th>";
            }
            echo "</tr></thead><tbody>";

            $aliments = query($mysqli, "SELECT * FROM `ALIMENT`;");

            while ($aliment = mysqli_fetch_assoc($aliments)) {

                // recherche tous les cocktails selon l'ingrédient (aliment)  recherché
                $nbCocktails = query($mysqli, 'SELECT * FROM `ALIMENT_APPARTIENT` WHERE fiAliment=' . $aliment["idAliment"] . ';');

                $nomAliment = $aliment["nomAliment"];
                // enregistre le nombre de ligne de la requête
                $array[$nomAliment] = mysqli_num_rows($nbCocktails);
            }

            // array assortie par le nombre de cocktail
            asort($array);

            // si on clique sur la tête du tableau de la 1e colonne --> Nb de cocktail
            // array assortie par la valeur
            if (isset($_GET['sort']) && $_GET['sort'] == "recette") {
                if (isset($_GET['order'])) {
                    // ordre décroissante
                    if ($_GET['order'] == 'DESC') {
                        arsort($array);
                    }
                    else{
                        // ordre croissante
                        asort($array);
                    }
                }
            }

            // si on clique sur la tête du tableau de la 2e colonne --> Aliment
            // array assortie par la clé
            if (isset($_GET['sort']) && $_GET['sort'] == "aliment") {
                if (isset($_GET['order'])) {
                    // ordre croissante
                    if ($_GET['order'] == 'ASC') {
                        krsort($array);
                    } else {
                        // ordre décroissante
                        ksort($array);
                    }
                }
            }

            foreach ($array as $nomAliment => $nbCocktails){
                    echo "<tr><td>";
                    echo $nbCocktails;
                    echo "</td>
                       <td ><a href='index.php?p=cocktail&aliment=$nomAliment'>$nomAliment</a></td>
                       </tr>";

            }

            echo "</tbody></table>";
            mysqli_close($mysqli);
        }

    }

    // afficher tous les cocktails selon ingrédient demandé
    function showCocktailByAliment($aliment)
    {
        include("config.inc.php");
        echo "<h1>Liste des cocktails contient $aliment</h1>";

        if (!empty($host) and !empty($user) and !empty($pass) and !empty($base)) {
            $mysqli = mysqli_connect($host, $user, $pass) or die("Problème de création de la base :" . mysqli_connect_error());

            query($mysqli, "USE $base");

            // recherche id tous aliment demandé
            $aliments = query($mysqli, 'SELECT * FROM `ALIMENT` WHERE nomAliment="' . $aliment . '";');

            while ($alimentId = mysqli_fetch_assoc($aliments)) {

                // recherche tous les cocktails contenant l'aliment demandé
                $alimentsAppartient = query($mysqli, "SELECT * FROM `ALIMENT_APPARTIENT` WHERE fiAliment=" . $alimentId["idAliment"] . ";");

                // si il n'existe aucune cocktail qui contient ce aliment
                if(mysqli_num_rows($alimentsAppartient)==0) {
                    echo "<p class='alert alert-info'>Aucun cocktail connu!!</p>";
                }

                // le cas si il existe
                // recherche pour tous les cocktails trouvé dans la requête précédente
                while ($alimentAppartient = mysqli_fetch_assoc($alimentsAppartient)) {

                    // recherche tous les détails du cocktail selon id
                    $cocktails = query($mysqli, "SELECT * FROM `COCKTAIL` WHERE idCocktail=" . $alimentAppartient["fiCocktail"] . ";");

                    while ($cocktail = mysqli_fetch_assoc($cocktails)) {
                        $title = $cocktail['titre'];
                        $id = $cocktail["idCocktail"];
                        $titleArrayDesignation = getDesignation($title);
                        $image = getImageByTitle($titleArrayDesignation);

                        echo "<article>
                                   <header>
                                        <h3><a href='index.php?p=cocktail&id=$id'>$title</a></h3>";
                        //si article est un favori
                        if (isset($_SESSION['favorites'])) {
                            if (array_key_exists($id, $_SESSION['favorites'])) {
                                echo "<a href='index.php?p=favorites&event=remove&id=$id'>
                                    <img class='coeur' name=$id src='Photos/coeur_full.png' width='25' height='25'></a>";
                            } else {
                                echo "<a href='index.php?p=favorites&event=add&id=$id'>
                                    <img class='coeur' name=$id src='Photos/coeur_vide.png' width='25' height='25'></a>";
                            }
                        } else {
                            echo "<img class='coeur' name=$id src='Photos/coeur_vide.png' width='25' height='25'>";
                        }

                        echo "</header>
                                 <img src='Photos/$image' width='90'/>
                          <ul>";


                        // affiche tous les ingrédients du cocktail
                        $idCocktail = query($mysqli, 'SELECT * FROM ALIMENT_APPARTIENT WHERE fiCocktail=' . $id . ';');

                        while ($cocktail_result = mysqli_fetch_assoc($idCocktail)) {
                            $aliment = query($mysqli, 'SELECT * FROM ALIMENT WHERE idAliment=' . $cocktail_result["fiAliment"] . ';');
                            while ($index = mysqli_fetch_assoc($aliment)) {
                                $ingredient = $index["nomAliment"];
                                echo "<li><a href='index.php?p=cocktail&aliment=$ingredient'>$ingredient</a></li>";
                            }
                        }
                        echo "</ul></article>";
                    }

                }
            }
            mysqli_close($mysqli);
        }
    }


    //affiche tous les article
    function showAllCocktails()
    {
        include("config.inc.php");
        echo "<h1>Liste des cocktails</h1>";

        if (!empty($host) and !empty($user) and !empty($pass) and !empty($base)) {
            $mysqli = mysqli_connect($host, $user, $pass) or die("Problème de création de la base :" . mysqli_connect_error());

            query($mysqli, "USE $base");

            $cocktails = query($mysqli, "SELECT * FROM `COCKTAIL`");


            while ($cocktail = mysqli_fetch_assoc($cocktails)) {
                $title = $cocktail['titre'];
                $id = $cocktail["idCocktail"];
                $titleArrayDesignation = getDesignation($title);
                $image = getImageByTitle($titleArrayDesignation);

                echo "<article>
                               <header>
                                    <h3><a href='index.php?p=cocktail&id=$id'>$title</a></h3>";
                //si article est un favori
                if($_SESSION["connecte"]=="true"){
                    if (!empty($_SESSION['favorites']) && array_key_exists($id, $_SESSION['favorites'])) {
                        echo "<a href='index.php?p=favorites&event=remove&id=$id'>
                                <img class='coeur' name=$id src='Photos/coeur_full.png' width='25' height='25'></a>";
                    }
                    else{
                        echo "<a href='index.php?p=favorites&event=add&id=$id'>
                                <img class='coeur' name=$id src='Photos/coeur_vide.png' width='25' height='25'></a>";

                    }
                }
                else{
                    echo "<img class='coeur' name=$id src='Photos/coeur_vide.png' width='25' height='25'>";
                }

                echo "</header>
                             <img src='Photos/$image' width='90'/>
                      <ul>";

                // affiche tous les ingrédients du cocktail
                $idCocktail = query($mysqli, 'SELECT * FROM ALIMENT_APPARTIENT WHERE fiCocktail=' . $id . ';');

                while ($cocktail_result = mysqli_fetch_assoc($idCocktail)) {
                    $aliment = query($mysqli, 'SELECT * FROM ALIMENT WHERE idAliment=' . $cocktail_result["fiAliment"] . ';');
                    while ($index = mysqli_fetch_assoc($aliment)) {
                        $ingredient = $index["nomAliment"];
                        echo "<li><a href='index.php?p=cocktail&aliment=$ingredient'>$ingredient</a></li>";
                    }
                }
                echo "</ul></article>";

            }
            mysqli_close($mysqli);
        }
    }

        //affiche la partie detaillée du article en question
        function showCocktailsDetails($id)
        {
            include("config.inc.php");

            if (!empty($host) and !empty($user) and !empty($pass) and !empty($base)) {
                $mysqli = mysqli_connect($host, $user, $pass) or die("Problème de création de la base :" . mysqli_connect_error());

                query($mysqli, "USE $base");

                $cocktail = query($mysqli, 'SELECT * FROM `COCKTAIL` WHERE idCocktail=' . $id . ';');
                $cocktail = mysqli_fetch_assoc($cocktail);

                $fullTitle = $cocktail['titre'];
                $titleArrayDesignation = getDesignation($fullTitle);
                $image = getImageByTitle($titleArrayDesignation);
                echo "<header class='details'>
                                <h1><a href='index.php?p=cocktail&id=$id'>$fullTitle</a></h1>";

                //si article est un favori
                if($_SESSION["connecte"]=="true"){
                    if (!empty($_SESSION['favorites']) && array_key_exists($id, $_SESSION['favorites'])) {
                        echo "<a href='index.php?p=favorites&event=remove&id=$id'>
                                <img class='coeur' name=$id src='Photos/coeur_full.png' width='25' height='25'></a>";
                    } else {
                        echo "<a href='index.php?p=favorites&event=add&id=$id'>
                                <img class='coeur' name=$id src='Photos/coeur_vide.png' width='25' height='25'></a>";
                    }
                } else {
                    echo "<img class='coeur' name=$id src='Photos/coeur_vide.png' width='25' height='25'>";
                }

                echo "</header>
                          <img src='Photos/$image' width='200px'/>
                          <h2>Ingrédients:</h2>
                          <ul>";

                $ingredients = query($mysqli, 'SELECT * FROM `ALIMENT_APPARTIENT` WHERE fiCocktail=' . $id . ';');

                // affiche tous les ingrédients du cocktail avec la quantité
                while ($ingredient = mysqli_fetch_assoc($ingredients)) {
                    $quantite = $ingredient['quantité'];
                    echo "<li>$quantite</li>";
                }

                // affiche la préparation du cocktail
                $preparation = $cocktail['preparation'];
                echo "</ul> <h2>Préparation:</h2> <div>$preparation</div>";

                mysqli_close($mysqli);
            }

        }

        // affiche tous les cocktails favories -->  a l aide de la session
        function showFavoritesCocktails(){
            include("config.inc.php");
            echo "<h1>Liste des cocktails favorites</h1>";

            if (!empty($host) and !empty($user) and !empty($pass) and !empty($base)) {
                $mysqli = mysqli_connect($host, $user, $pass) or die("Problème de création de la base :" . mysqli_connect_error());

                query($mysqli, "USE $base");

                foreach($_SESSION['favorites'] as $id => $title){
                        $titleArrayDesignation = getDesignation($title);
                        $image = getImageByTitle($titleArrayDesignation);

                        echo "<article>
                                   <header>
                                        <h3><a href='index.php?p=cocktail&id=$id'>$title</a></h3>";
                        //si article est un favori
                        echo "<a href='index.php?p=favorites&event=remove&id=$id'>
                                    <img class='coeur' name=$id src='Photos/coeur_full.png' width='25' height='25'></a>";

                        echo "</header>
                                 <img src='Photos/$image' width='90'/>
                          <ul>";

                        $alimentAppartient = query($mysqli, 'SELECT * FROM ALIMENT_APPARTIENT WHERE fiCocktail=' . $id . ';');

                        while ($cocktail_aliment = mysqli_fetch_assoc($alimentAppartient)) {
                            $aliment = query($mysqli, 'SELECT * FROM ALIMENT WHERE idAliment=' . $cocktail_aliment["fiAliment"] . ';');
                            while ($index = mysqli_fetch_assoc($aliment)) {
                                $ingredient = $index["nomAliment"];
                                echo "<li><a href='index.php?p=cocktail&aliment=$ingredient'>$ingredient</a></li>";
                            }
                        }
                        echo "</ul></article>";

                    }
                    mysqli_close($mysqli);
                }
        }

?>