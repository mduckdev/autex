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

    <div id="navcontainer">
        <a href="clients.php?rent=on">Wypożycz samochód</a>
        <a href="rents.php?unreturned=on">Zwróć samochód</a>
        <a href="rents.php">Wypożyczenia</a>
        <a href="clients.php">Klienci</a>
        <a href="offer.php">Samochody</a>


        <?php
        if (isLoggedIn()) {
            echo ("<a href=\"account.php\">Konto</a>");
            echo ("<a href=\"logout.php\" id=\"last_item\">Wyloguj</a>");
        } else {
            echo ("<a href=\"login.php\" id=\"last_item\">Logowanie | Rejestracja</a>");
        }
        ?>
    </div>
</nav>