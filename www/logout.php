<?php
require(dirname(__FILE__) . "/" . "../www/includes/session.php");

session_unset();
session_destroy();
echo ("Wylogowano");
header("Location: /autex/www/index.php");
