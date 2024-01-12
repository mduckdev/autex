<?php

function flota_gen()
{
    require(dirname(__FILE__) . "/" . "../www/includes/db.php");
    mysqli_set_charset($mysqli, "utf8");

    $samochody = file_get_contents("lists/samochody.txt");
    $samochody = explode("\n", $samochody);


    function generateRandomString($length = 5)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }


    /* Prepared statement, stage 1: prepare */
    $stmt = $mysqli->prepare("INSERT INTO flota(marka,model,rocznik,kolor,przebieg,moc_km,nr_rej,cena) VALUES (?,?,?,?,?,?,?,?)");

    $kolory = ["czerwony", "niebieski", "srebrny", "biały", "czarny", "błękitny"];

    for ($i = 0; $i < 200; $i++) {
        $samochod = explode(":", $samochody[array_rand($samochody)]);

        $marka = mysqli_real_escape_string($mysqli, $samochod[0]);
        $model = mysqli_real_escape_string($mysqli, $samochod[1]);
        $model = str_replace("\\r", "", $model);
        $rocznik = random_int(2012, 2021);
        $kolor = $kolory[array_rand($kolory)];
        $przebieg = random_int(10, 300000);
        $moc_km = random_int(90, 200);
        $nr_rej = "WR" . (generateRandomString());
        $cena = (random_int(2, 10)) * 100;

        //echo "$marka,$model,$rocznik,$kolor,$przebieg,$moc_km <br>";
        $stmt->bind_param("ssisiiss", $marka, $model, $rocznik, $kolor, $przebieg, $moc_km, $nr_rej, $cena);




        $stmt->execute();
    }
}
#flota_gen();
