<?php

require("session.php");

function isLoggedIn() //funkcja do sprawdzenia czy użytkownik jest zalogowany
{
    if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != 1) {
        return false;
    } else {
        return true;
    }
}

function isEmployee()
{
    if(!isset($_SESSION['employee']) || $_SESSION['employee'] != 1){
        return false;
    } else {
        return true;
    }
}

function requireAuth() // funkcja do wymuszenia zalogowania przez użytkownika
{
    if (!(isLoggedIn())) {
        header("Location: /autex/www/login.php");
        die();
    }
}

function requireEmployee(){
    if(!isEmployee()){
        header("Location: /autex/www/index.php");
        die();
    }
}