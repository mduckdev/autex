<?php
function wypozyczenia_gen()
{
    //zwrocone auta
    $mysqli = mysqli_connect("localhost", "root", "", "wypozyczalnia");
    mysqli_set_charset($mysqli, "utf8");

    $cars_id_result = $mysqli->query("SELECT id FROM flota;");
    $cars_ids = $cars_id_result->fetch_all(MYSQLI_ASSOC);
    $client_id_result = $mysqli->query("SELECT id FROM klienci;");
    $client_ids = $client_id_result->fetch_all(MYSQLI_ASSOC);
    $stmt = $mysqli->prepare("INSERT INTO wypozyczenia(id_auta,id_klienta,data_wypozyczenia,data_zwrotu) VALUES (?,?,?,?)");

    for ($i = 0; $i < 150; $i++) {
        $cars_ids_copy = $cars_ids;
        $index = array_rand($cars_ids_copy, 1);
        $id_auta = $cars_ids_copy[$index];
        unset($cars_ids_copy[$index]);

        $id_klienta = $client_ids[array_rand($client_ids, 1)];

        $start_date_timestamp = random_int(1641034364, 1672572602); // Sat Jan 01 2022-Sun Jan 01 2023
        $end_date_timestamp = random_int($start_date_timestamp + (86400 * 2), $start_date_timestamp + (86400 * 14));
        $start_date = date("Y-m-d", $start_date_timestamp);
        $end_date = date("Y-m-d", $end_date_timestamp);

        $stmt->bind_param("iiss", $id_auta["id"], $id_klienta["id"], $start_date, $end_date);
        $stmt->execute();
        //echo "ID auta:" . $id_auta["id"] . " ID klienta: " . $id_klienta["id"] . " Początek: " . date("d.m.Y H:i", $start_date_timestamp) . " <br>";
        //echo "Koniec: " . date("d.m.Y H:i", $end_date_timestamp) . " <br>";
    }
    for ($i = 0; $i < 150; $i++) {
        $cars_ids_copy = $cars_ids;
        $index = array_rand($cars_ids_copy, 1);
        $id_auta = $cars_ids_copy[$index];
        unset($cars_ids_copy[$index]);

        $id_klienta = $client_ids[array_rand($client_ids, 1)];


        $start_date_timestamp = random_int(1609498364, 1641034364); // Fri Jan 01 2023-Sat Jan 01 2022
        $end_date_timestamp = random_int($start_date_timestamp + (86400 * 2), $start_date_timestamp + (86400 * 14));

        $start_date = date("Y-m-d", $start_date_timestamp);
        $end_date = date("Y-m-d", $end_date_timestamp);
        $stmt->bind_param("iiss", $id_auta["id"], $id_klienta["id"], $start_date, $end_date);

        $stmt->execute();
        //echo "ID:" . $id["id"] . " Początek: " . date("d.m.Y H:i", $start_date_timestamp) . " <br>";
        //echo "Koniec: " . date("d.m.Y H:i", $end_date_timestamp) . " <br>";
    }
    //zwrocone auta

    //jeszcze niezwrocone auta

    for ($i = 0; $i < 35; $i++) {
        $cars_ids_copy = $cars_ids;
        $index = array_rand($cars_ids_copy, 1);
        $id_auta = $cars_ids_copy[$index];
        unset($cars_ids_copy[$index]);

        $current_date = time();
        $start_date_timestamp = random_int($current_date - (86400 * 6), $current_date);
        $start_date = date("Y-m-d", $start_date_timestamp);
        $id_klienta = $client_ids[array_rand($client_ids, 1)];

        $stmt2 = $mysqli->prepare("INSERT INTO wypozyczenia(id_auta,id_klienta,data_wypozyczenia) VALUES (?,?,?)");



        $stmt2->bind_param("iis", $id_auta["id"], $id_klienta["id"], $start_date);

        $stmt2->execute();

        //echo "Początek: " . date("d.m.Y H:i", $start_date_timestamp) . " <br>";
        //echo "Koniec: " . date("d.m.Y H:i", $end_date_timestamp) . " <br>";
    }

    //jeszcze niezwrócone auta
}







