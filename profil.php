<?php
    $complet=false;
    $ClassLogin='ok';
    $ClassPWD='ok';
    $ClassPrenom='ok';
    $ColorPrenom="";
    $ColorLogin="";
    $ColorPWD="";


    // Vérification du formulaire
    if(isset($_POST['submit']))  // le formulaire vient d'être soumis
    {
        //verification login
        if(!isset($_POST['login'])) //login non positionnee
        {
            $ClassLogin='error';
            $ColorLogin="background-color:red";
        }
        else
        {
            //  si le login contient autre chose que des lettres non accentué et en majuscule ou en minuscule et/ou des chiffres
            if($_POST['login'] != $_SESSION['login'] )             
            {
                $ClassLogin='error';
                $ColorLogin="background-color:red";

            }

        }

        //verification du mot de passe
        if(!isset($_POST['pwd'])) //mot de passe non positionnee
        {
            $ClassPWD='error';
            $ColorPWD="background-color:red";
        }if (!preg_match("#^(.+)$#",trim($_POST['pwd'])))
        {
            $ClassPWD='error';
            $ColorPWD="background-color:red";
        }
        if ($_SESSION['pwd'] != $_POST['pwd'])
        {
            $_SESSION['majPWD'] = "oui";
        }
        else
        {
            $_SESSION['majPWD'] = "non";
        }
        

        //verification du prenom
        
        //prenom avec lettres minuscules et/ou de lettres MAJUSCULES ainsi que les caractères « - », « » (espace) et « ’ ». 
        //Les lettres peuvent être accentuées. Tiret et apostrophe forcément encadré par deux lettres
        //plusieurs espaces sont possibles entre deux parties de nom.
        if (!preg_match("#^([A-zÀ-ÿ]+|[A-zÀ-ÿ]+['\-]?[A-zÀ-ÿ]+|[A-zÀ-ÿ]+[\s]+[A-zÀ-ÿ]+)$#",trim($_POST['prenom']))&& $_POST['prenom'] != "")
        {
            $ClassPrenom='error';
            $ColorPrenom="background-color:red";
        }


        //verification si toutes les classes sont okay donc mise a true de $complet
        if ( ($ClassLogin=="ok")
            && ($ClassPWD=="ok")
            && ($ClassPrenom=="ok")) {
            // le formulaire est complet 
            $complet = true;

            $_SESSION['login'] = $_POST["login"];
            $_SESSION['pwd'] = $_POST["pwd"];
            $_SESSION['prenom'] = $_POST["prenom"];
            $_SESSION['age'] = $_POST["age"];
            $_SESSION["connecte"] = "true";

            if (!empty($host) and !empty($user) and !empty($pass) and !empty($base)) {
                $mysqli = mysqli_connect($host, $user, $pass) or die("Problème de création de la base :" . mysqli_connect_error());

                query($mysqli, "USE $base");
                // modifier mots de passe
                query($mysqli, 'UPDATE USER SET password="' . $_SESSION["pwd"] . '" WHERE userName="' . $_SESSION["login"] . '";');

                //modifier prenom
                if ($_SESSION['prenom'] == "") {
                    query($mysqli, 'UPDATE USER SET prenom=NULL WHERE userName="' . $_SESSION["login"] . '";');
                } else {
                    query($mysqli, 'UPDATE USER SET prenom="' . $_SESSION["prenom"] . '" WHERE userName="' . $_SESSION["login"] . '";');

                }

                //modifier age
                if ($_SESSION['age'] == "") {
                    query($mysqli, 'UPDATE USER SET age=NULL WHERE userName="' . $_SESSION["login"] . '";');
                } else {
                    query($mysqli, 'UPDATE USER SET age="' . $_SESSION["age"] . '" WHERE userName="' . $_SESSION["login"] . '";');

                }

                mysqli_close($mysqli);
            }
        }
    }

    // si le formulaire est bien rempli afficher c'est complet sinon on affiche le formulaire
    if($complet)
    {
      echo "<h2 class='alert alert-success'>Vous avez bien rempli le formulaire! Les données sont modifiées!</h2>";
    }
    else 
    {
        echo "<h1>Formulaire pour l'inscription</h1>";
        if (isset($_POST['submit'])) {
            echo "<h2 class='alert alert-danger'>Veuillez completer tous les champs, svp.</h2>";
        } 
        else 
        {
            echo "<h2 class='alert alert-info'>Veuillez remplir le formulaire</h2>";
        } ?>

        <form method="post" class="form-horizontal" action="#">
            <div class="form-group">
                <label class="control-label col-sm-2" for="login">Login:</label>
                <input type="text" class="<?php echo $ClassLogin; ?>" name='login' value ="<?php echo (isset($_POST['login'])?$_POST['login']:$_SESSION['login']); ?>" style ="<?php echo "$ColorLogin" ?>">
            </div>
            <div class="form-group">
                <label class="control-label col-sm-2" for="pwd">Mot de passe:</label>
                <input type="password" name="pwd" class="<?php echo $ClassPWD; ?>" value="<?php echo (isset($_POST['pwd'])?$_POST['pwd']:$_SESSION['pwd']); ?>" style ="<?php echo "$ColorPWD" ?>">
            </div>

            <div class="form-group">
                <label class="control-label col-sm-2" for="prenom">Prénom:</label>

                <input type="text" name="prenom" class="<?php echo $ClassPrenom; ?>" value="<?php echo (isset($_POST['prenom'])?$_POST['prenom']:$_SESSION['prenom']); ?>" style ="<?php echo "$ColorPrenom" ?>">

            </div>

            <div class="form-group">
                <label class="control-label col-sm-2" for="age">Age:</label>

                <input type="number" name="age"  value="<?php echo (isset($_POST['age'])?$_POST['age']:$_SESSION['age']); ?>" >

            </div>


            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" name="submit" value="Valider" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>

        <?php
    }
?>
