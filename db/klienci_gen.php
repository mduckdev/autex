<?php

function klienci_gen()
{
    require(dirname(__FILE__) . "/" . "../www/includes/db.php");
    mysqli_set_charset($mysqli, "utf8");

    $imiona = file_get_contents("lists/imiona.txt");
    $imiona = explode("\n", $imiona);
    $nazwiska = file_get_contents("lists/nazwiska.txt");
    $nazwiska = explode("\n", $nazwiska); // załadowanie list z imionami i nazwiskami i zrobienie z nich tablic

    $stmt = $mysqli->prepare("INSERT INTO klienci(imie,nazwisko,nr_tel,email,data_ur) VALUES (?,?,?,?,?)"); //zapytanie

    for ($i = 0; $i < 250; $i++) {
        $imie = mysqli_real_escape_string($mysqli, $imiona[array_rand($imiona)]); // generowanie losowych danych w pętli
        $nazwisko = mysqli_real_escape_string($mysqli, $nazwiska[array_rand($nazwiska)]);
        $nr_tel = mysqli_real_escape_string($mysqli, random_int(100000000, 999999999));
        $email = mysqli_real_escape_string($mysqli, mb_strtolower(mb_substr($imie, 0, 3)) . random_int(1000, 9999) . "@gmail.com");
        $data_ur = mysqli_real_escape_string($mysqli, random_int(1940, 2005) . "-" . random_int(1, 12) . "-" . random_int(1, 28));

        $imie = str_replace("\\r", "", $imie); // generując na windowsie imiona zawierały w sobie znaki carriage return
        $nazwisko = str_replace("\\r", "", $nazwisko);

        #echo "$imie $nazwisko $nr_tel $email $data_ur <br>";
        $stmt->bind_param("sssss", $imie, $nazwisko, $nr_tel, $email, $data_ur); //przypisanie parametrów do zapytania
        $stmt->execute(); //wykonanie zapytania
    }
}
