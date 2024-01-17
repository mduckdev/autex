<?php
require(dirname(__FILE__) . "/" . "../www/includes/session.php");

session_unset();
session_destroy();
echo ("Wylogowano"); //skrypt odpowiadający za wylogowanie użytkownika, sesja jest niszczona a użytkownik przekierowany na główną stronę
header("Location: /autex/www/index.php");
