<?php
require(dirname(__FILE__) . "/" . "../www/includes/session.php");
include_once(dirname(__FILE__) . "/" . "../www/includes/auth.php");
require(dirname(__FILE__) . "/" . "./includes/csp.php");

?>

<nav id="navbar">
    <a href="./index.php">
        <div id="navlogo">
            <img src="./img/car-512.png" alt="Autex logo">
            Autex
        </div>
    </a>

    <div id="navcontainer"> <!-- pasek nawigacyjny -->
        


        <?php // warunkowe wyświetlanie na podstawie tego czy użytkownik jest zalogowany, jeśli jest ma opcję wylogowania, jeśli nie to ma link do formularza logowania
        if (isLoggedIn()) {
            if(isEmployee()){
                include "./employee_bar.php";
            } 
            echo("<a href=\"my_rents.php\">Moje wypożyczenia</a>");
            echo("<a href=\"profile.php\">Profil</a>");
            echo ("<a href=\"logout.php\" id=\"last_item\">Wyloguj</a>");
        } else {
            echo ("<a href=\"login.php\" id=\"last_item\">Logowanie | Rejestracja</a>");
        }
        ?>
    </div>
</nav>