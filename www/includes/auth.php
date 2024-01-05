<?php

require("session.php");

if (!(isset($_SESSION['login']) && $_SESSION['login'] != '')) {
    header ("Location: /autex/www/login.php");
    die();
} else{

    echo "Zalogowany"; 
}

?>