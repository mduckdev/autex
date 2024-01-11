<?php

require("session.php");

function isLoggedIn()
{
    if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != 1) {
        return false;
    } else {
        return true;
    }
}

function requireAuth()
{
    if (!(isLoggedIn())) {
        header("Location: /autex/www/login.php");
        echo ("XD");
        die();
    }
}
