<?php
require(dirname(__FILE__) . "/" . "../../vendor/autoload.php");


use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
?>