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
        <a href="clients.php?rent=on">Wypożycz samochód</a> <!-- lista klientów z włączonym trybem wynajmu -->
        <a href="rents.php?unreturned=on">Zwróć samochód</a><!-- lista samochodów z filtrem niezwróconych  -->
        <a href="rents.php">Wypożyczenia</a> <!--reszta linków -->
        <a href="clients.php">Klienci</a>
        <a href="offer.php">Samochody</a>


        <?php // warunkowe wyświetlanie na podstawie tego czy użytkownik jest zalogowany, jeśli jest ma opcję wylogowania, jeśli nie to ma link do formularza logowania
        if (isLoggedIn()) {
            echo ("<a href=\"logout.php\" id=\"last_item\">Wyloguj</a>");
        } else {
            echo ("<a href=\"login.php\" id=\"last_item\">Logowanie | Rejestracja</a>");
        }
        ?>
    </div>
</nav>