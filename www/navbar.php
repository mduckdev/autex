<?php
require(dirname(__FILE__) . "/" . "../www/includes/session.php");

include_once(dirname(__FILE__) . "/" . "../www/includes/auth.php");

?>

<nav id="navbar">
    <a href="./index.php">
        <div id="navlogo">
            <img src="./img/car-512.png" alt="Autex logo">
            Autex
        </div>
    </a>

    <div id="navcontainer">
        <a href="#">Wypożycz samochód</a>
        <a href="#">Zwróć samochód</a>
        <a href="rents.php">Wypożyczenia</a>

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