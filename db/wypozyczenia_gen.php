<?php
function wypozyczenia_gen()
{
    require(dirname(__FILE__) . "/" . "../www/includes/db.php");
    mysqli_set_charset($mysqli, "utf8");

    $cars_id_result = $mysqli->query("SELECT id,cena FROM flota;"); //pobranie z bazy id aut oraz ich cen
    $cars_ids = $cars_id_result->fetch_all(MYSQLI_ASSOC);

    $client_id_result = $mysqli->query("SELECT id FROM klienci;"); // pobranie id klientów
    $client_ids = $client_id_result->fetch_all(MYSQLI_ASSOC); //zapisanie ich do zmiennej


    $stmt = $mysqli->prepare("INSERT INTO wypozyczenia(id_auta,id_klienta,data_wypozyczenia,data_zwrotu,cena) VALUES (?,?,?,?,?)"); //zapytanie wstawiające

    $cars_ids_copy = $cars_ids;
    $client_ids_copy = $client_ids;
    for ($i = 0; $i < 150; $i++) { // pierwsza pętla wstawiająca wypożyczenia
        shuffle($cars_ids_copy); // pomieszanie tablicy z id aut
        $auto = array_pop($cars_ids_copy); //pobranie pierwszego elementu z tablicy
        $id_auta = $auto["id"];
        $cena = $auto["cena"];

        shuffle($client_ids_copy);
        $id_klienta = array_pop($client_ids_copy)["id"];
        $start_date_timestamp = random_int(1641034364, 1672572602); // Sat Jan 01 2022-Sun Jan 01 2023
        $end_date_timestamp = random_int($start_date_timestamp + (86400 * 2), $start_date_timestamp + (86400 * 14)); // wygenerowanie daty zakończenia na podstawie daty rozpoczęcia, wypozyczenie trwa od 2 do 14 dni
        $start_date = date("Y-m-d h:m:s", $start_date_timestamp);
        $end_date = date("Y-m-d h:m:s", $end_date_timestamp);
        $days = ($end_date_timestamp - $start_date_timestamp) / 86400;
        $cena_koncowa = $days * $cena; // obliczenie ceny wypożyczenia

        $stmt->bind_param("iissi", $id_auta, $id_klienta, $start_date, $end_date, $cena_koncowa);
        $stmt->execute();
        //echo "ID auta:" . $id_auta["id"] . " ID klienta: " . $id_klienta["id"] . " Początek: " . date("d.m.Y H:i", $start_date_timestamp) . " <br>";
        //echo "Koniec: " . date("d.m.Y H:i", $end_date_timestamp) . " <br>";
    }
    $cars_ids_copy = $cars_ids;
    $client_ids_copy = $client_ids;

    for ($i = 0; $i < 150; $i++) { // ta pętla powtarza się jeszcze raz ale z innymi datami
        shuffle($cars_ids_copy);
        $auto = array_pop($cars_ids_copy);
        $id_auta = $auto["id"];
        $cena = $auto["cena"];


        shuffle($client_ids_copy);
        $id_klienta = array_pop($client_ids_copy)["id"];


        $start_date_timestamp = random_int(1609498364, 1641034364); // Fri Jan 01 2023-Sat Jan 01 2022
        $end_date_timestamp = random_int($start_date_timestamp + (86400 * 2), $start_date_timestamp + (86400 * 14));

        $start_date = date("Y-m-d h:m:s", $start_date_timestamp);
        $end_date = date("Y-m-d h:m:s", $end_date_timestamp);

        $days = ($end_date_timestamp - $start_date_timestamp) / 86400;
        $cena_koncowa = $days * $cena;

        $stmt->bind_param("iissi", $id_auta, $id_klienta, $start_date, $end_date, $cena_koncowa);

        $stmt->execute();
        //echo "ID:" . $id["id"] . " Początek: " . date("d.m.Y H:i", $start_date_timestamp) . " <br>";
        //echo "Koniec: " . date("d.m.Y H:i", $end_date_timestamp) . " <br>";
    }
    //zwrocone auta

    //jeszcze niezwrocone auta
    $cars_ids_copy = $cars_ids;

    for ($i = 0; $i < 35; $i++) { // pętla do wygenerowania niezwróconych wypożyczeń
        shuffle($cars_ids_copy);
        $id_auta = array_pop($cars_ids_copy)["id"];

        $current_date = time();
        $start_date_timestamp = random_int($current_date - (86400 * 6), $current_date);
        $start_date = date("Y-m-d h:m:s", $start_date_timestamp);

        shuffle($client_ids_copy);
        $id_klienta = array_pop($client_ids_copy)["id"];

        $stmt = $mysqli->prepare("INSERT INTO wypozyczenia(id_auta,id_klienta,data_wypozyczenia) VALUES (?,?,?)");
        $stmt2 = $mysqli->prepare("UPDATE flota SET dostepny=0 WHERE id=?"); // zaktualizowanie bazy danych że wypożyczone auto nie jest już dostępne


        $stmt->bind_param("iis", $id_auta, $id_klienta, $start_date);
        $stmt2->bind_param("i", $id_auta);

        $stmt->execute();
        $stmt2->execute();

        //echo "Początek: " . date("d.m.Y H:i", $start_date_timestamp) . " <br>";
        //echo "Koniec: " . date("d.m.Y H:i", $end_date_timestamp) . " <br>";
    }

    //jeszcze niezwrócone auta
}
