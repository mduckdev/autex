<?php
function wypozyczenia_gen()
{
    //zwrocone auta
    require(dirname(__FILE__) . "/" . "../www/includes/db.php");
    mysqli_set_charset($mysqli, "utf8");

    $cars_id_result = $mysqli->query("SELECT id FROM flota;");
    $cars_ids = $cars_id_result->fetch_all(MYSQLI_ASSOC);

    $client_id_result = $mysqli->query("SELECT id FROM klienci;");
    $client_ids = $client_id_result->fetch_all(MYSQLI_ASSOC);


    $stmt = $mysqli->prepare("INSERT INTO wypozyczenia(id_auta,id_klienta,data_wypozyczenia,data_zwrotu) VALUES (?,?,?,?)");

    $cars_ids_copy = $cars_ids;
    $client_ids_copy = $client_ids;
    for ($i = 0; $i < 150; $i++) {
        shuffle($cars_ids_copy);
        $id_auta = array_pop($cars_ids_copy)["id"];

        shuffle($client_ids_copy);
        $id_klienta = array_pop($client_ids_copy)["id"];

        $start_date_timestamp = random_int(1641034364, 1672572602); // Sat Jan 01 2022-Sun Jan 01 2023
        $end_date_timestamp = random_int($start_date_timestamp + (86400 * 2), $start_date_timestamp + (86400 * 14));
        $start_date = date("Y-m-d", $start_date_timestamp);
        $end_date = date("Y-m-d", $end_date_timestamp);

        $stmt->bind_param("iiss", $id_auta, $id_klienta, $start_date, $end_date);
        $stmt->execute();
        //echo "ID auta:" . $id_auta["id"] . " ID klienta: " . $id_klienta["id"] . " Początek: " . date("d.m.Y H:i", $start_date_timestamp) . " <br>";
        //echo "Koniec: " . date("d.m.Y H:i", $end_date_timestamp) . " <br>";
    }
    $cars_ids_copy = $cars_ids;
    for ($i = 0; $i < 150; $i++) {
        shuffle($cars_ids_copy);
        $id_auta = array_pop($cars_ids_copy)["id"];


        shuffle($client_ids_copy);
        $id_klienta = array_pop($client_ids_copy)["id"];


        $start_date_timestamp = random_int(1609498364, 1641034364); // Fri Jan 01 2023-Sat Jan 01 2022
        $end_date_timestamp = random_int($start_date_timestamp + (86400 * 2), $start_date_timestamp + (86400 * 14));

        $start_date = date("Y-m-d", $start_date_timestamp);
        $end_date = date("Y-m-d", $end_date_timestamp);
        $stmt->bind_param("iiss", $id_auta, $id_klienta, $start_date, $end_date);

        $stmt->execute();
        //echo "ID:" . $id["id"] . " Początek: " . date("d.m.Y H:i", $start_date_timestamp) . " <br>";
        //echo "Koniec: " . date("d.m.Y H:i", $end_date_timestamp) . " <br>";
    }
    //zwrocone auta

    //jeszcze niezwrocone auta
    $cars_ids_copy = $cars_ids;

    for ($i = 0; $i < 35; $i++) {
        shuffle($cars_ids_copy);
        $id_auta = array_pop($cars_ids_copy)["id"];

        $current_date = time();
        $start_date_timestamp = random_int($current_date - (86400 * 6), $current_date);
        $start_date = date("Y-m-d", $start_date_timestamp);

        shuffle($client_ids_copy);
        $id_klienta = array_pop($client_ids_copy)["id"];

        $stmt = $mysqli->prepare("INSERT INTO wypozyczenia(id_auta,id_klienta,data_wypozyczenia) VALUES (?,?,?)");
        $stmt2 = $mysqli->prepare("UPDATE flota SET dostepny=0 WHERE id=?");


        $stmt->bind_param("iis", $id_auta, $id_klienta, $start_date);
        $stmt2->bind_param("i", $id_auta);

        $stmt->execute();
        $stmt2->execute();

        //echo "Początek: " . date("d.m.Y H:i", $start_date_timestamp) . " <br>";
        //echo "Koniec: " . date("d.m.Y H:i", $end_date_timestamp) . " <br>";
    }

    //jeszcze niezwrócone auta
}
