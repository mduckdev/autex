<?php

$host="localhost";
$db = "wypozyczalnia";
$username="root";
$password="";

try{
    $mysqli = mysqli_connect($host, $username, $password, $db);
}catch(mysqli_sql_exception $error){
    die("Nie udało się połączyć z bazą danych: ".$error->getMessage());
}