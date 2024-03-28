<?php
    // eliminer tous les données enregistrés dans la session
    $_SESSION['connecte'] = "false";

    $_SESSION['login']	="";
    $_SESSION['pwd']	="";
    $_SESSION['prenom']	="";
    $_SESSION['age']	= "";
    $_SESSION['favorites'] = array();
    
    echo "<p class='alert alert-success'>Vous êtes déconnecter!</p>";

?>