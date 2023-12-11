<?php

$mysqli = mysqli_connect("localhost", "root", "", "wypozyczalnia");
mysqli_set_charset($mysqli, "utf8");

$imiona = file_get_contents("lists/imiona.txt");
$imiona = explode("\n",$imiona);
$nazwiska = file_get_contents("lists/nazwiska.txt");
$nazwiska = explode("\n",$nazwiska);


#print_r($imiona);


/* Prepared statement, stage 1: prepare */
$stmt = $mysqli->prepare("INSERT INTO klienci(imie,nazwisko,nr_tel,email,data_ur) VALUES (?,?,?,?,?)");

for($i =0 ; $i<1000;$i++){
    $imie = mysqli_real_escape_string($mysqli,$imiona[array_rand($imiona)]);
    $nazwisko = mysqli_real_escape_string($mysqli,$nazwiska[array_rand($nazwiska)]);
    $nr_tel = mysqli_real_escape_string($mysqli,"48".random_int(100000000,999999999));
    $email = mysqli_real_escape_string($mysqli,mb_strtolower(mb_substr($imie,0,3)).random_int(1000,9999)."@gmail.com");
    $data_ur = mysqli_real_escape_string($mysqli,random_int(1940,2005)."-".random_int(1,12)."-".random_int(1,29));
    
    $imie = str_replace("\\r","",$imie);
    $nazwisko = str_replace("\\r","",$nazwisko);
    $email = str_replace("\\\\r","",$email);
    
    
    echo "$imie $nazwisko $nr_tel $email $data_ur <br>";
    $stmt->bind_param("sssss", $imie,$nazwisko,$nr_tel,$email,$data_ur);
    $stmt->execute();
}



echo "aaa";
