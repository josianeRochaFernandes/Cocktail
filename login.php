<?php
    include("config.inc.php");

    if(isset($_POST['submit']) && isset($_POST['login']) && isset($_POST['pwd']))
    {
        if (($_POST['login'] !="") && ($_POST['pwd']!="") ) {

            //controller si le le login et mots de passe est correct
            if (!empty($host) and !empty($user) and !empty($pass) and !empty($base)) {
                $mysqli = mysqli_connect($host, $user, $pass) or die("Problème de création de la base :" . mysqli_connect_error());

                query($mysqli, "USE $base");

                $users = query($mysqli, 'SELECT * FROM `USER`;');
                if (mysqli_num_rows($users) == 0) {
                    echo "<p class='alert alert-danger'>Il faut vous inscrire</p>";
                }else{
                    $login = query($mysqli, 'SELECT * FROM `USER` WHERE userName="' . $_POST['login'] . '";');
                    if (mysqli_num_rows($login) == 0) {
                        echo "<p class='alert alert-danger'>Username non connu</p>";
                    } else {
                        $password = query($mysqli, 'SELECT * FROM `USER` WHERE userName="' . $_POST['login'] . '" AND password="' . $_POST['pwd'] . '";');
                        if (mysqli_num_rows($password) == 0) {
                            echo "<p class='alert alert-danger'>Mot de passe incorrect</p>";
                        }
                        else{
                            //enregistrer les données de l'utilisateur dans session
                            while($pwd= mysqli_fetch_assoc($password)){
                                $_SESSION["connecte"] = "true";
                                //sauvegarde des donnees
                                $_SESSION['login']	=$pwd["userName"];
                                $_SESSION['pwd']	=$pwd["password"];
                                $_SESSION['prenom']	=$pwd["prenom"];
                                $_SESSION['age']	= $pwd["age"];
                                $_SESSION['favorites'] = array();

                                $favorites = query($mysqli, 'SELECT * FROM `COCKTAIL_FAVORITE` WHERE fiUser="' . $_SESSION['login'] . '";');

                                while($favorites= mysqli_fetch_assoc($favorites)){
                                    $nomCocktail = query($mysqli, 'SELECT * FROM COCKTAIL WHERE idCocktail='.$favorites["fiCocktail"].';');
                                    while($cocktail= mysqli_fetch_assoc($nomCocktail)){
                                        $_SESSION['favorites'][$favorites["fiCocktail"]] = $cocktail["titre"];
                                    }
                                }

                                echo "<p class='alert alert-info'>Vous êtes connectés!</p>";
                            }
                        }
                    }
                }
            }
        }
        else
        {
            echo "<p class='alert alert-danger'>Merci de remplir les deux champs</p>";
        }
    }
    else
    {
        echo "<p class='alert alert-danger'>Merci de remplir les deux champs</p>";
    }

    ?>



