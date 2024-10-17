<?php
require(dirname(__FILE__) . "/" . "./includes/session.php");
include_once(dirname(__FILE__) . "/" . "./includes/auth.php");
requireAuth();
requireEmployee();
require(dirname(__FILE__) . "/" . "./includes/csp.php");

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/tables_search.css">
    <script defer src="./js/search.js"></script>

    <title>Wypożyczalnia samochodów autex</title>
</head>

<body>
    <?php
    require("navbar.php");

    ?>
    <div class="transparent_background">
        <div class="search_bar"> <!-- wyszukiwarka wypożyczeń -->
            <form action="" method="get">
                <select name="limit" id="limit">
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="150">150</option>
                    <option value="200">200</option>
                    <option value="500">500</option>

                </select>
                <input type="text" name="id_a" id="id_a" placeholder="ID auta">

                <input type="text" name="q" id="q" placeholder="Imię,nazwisko, marka lub model">

                <input type="text" name="id_k" id="id_k" placeholder="ID klienta">
                Niezwrócone <input type="checkbox" name="unreturned" id="unreturned" <?php //filtr niezwróconych
                                                                                        if (isset($_GET['unreturned']) && $_GET['unreturned'] == "on") {
                                                                                            echo ("checked=\"true\"");
                                                                                        }
                                                                                        ?>>
                <input type="submit" value="Filtruj">
            </form>
        </div>


        <div class="tableContainer">


            <?php
            require(dirname(__FILE__) . "/" . "./includes/db.php");
            $sql =
                "SELECT wypozyczenia.id,wypozyczenia.id_klienta,wypozyczenia.id_auta,
                    klienci.imie,klienci.nazwisko,
                    flota.marka,flota.model,
                    wypozyczenia.data_wypozyczenia,wypozyczenia.data_zwrotu,wypozyczenia.cena
                    FROM wypozyczenia
                    JOIN klienci ON klienci.id = wypozyczenia.id_klienta
                    JOIN flota on flota.id = wypozyczenia.id_auta
                    "; //zapytanie do znalezienia wszystkich wypożyczeń i danych klienta ia auta z nimi powiazanych


            if (!isset($_GET['limit']) || !is_numeric($_GET['limit']) || intval($_GET['limit']) <= 0) // sprawdzanie czy ustawiony jest limit id klienta i id auta do przeszukania
                $limit = 25;
            else
                $limit = mysqli_real_escape_string($mysqli, intval($_GET['limit']));

            if (!isset($_GET['id_a']) || !is_numeric($_GET['id_a']) || intval($_GET['id_a']) <= 0)
                $id_a = null;
            else
                $id_a = mysqli_real_escape_string($mysqli, intval($_GET['id_a']));

            if (!isset($_GET['id_k']) || !is_numeric($_GET['id_k']) || intval($_GET['id_k']) <= 0)
                $id_k = null;
            else
                $id_k = mysqli_real_escape_string($mysqli, intval($_GET['id_k']));

            if (!isset($_GET['unreturned']) || $_GET['unreturned'] != "on") //sprawdzanie filtru niezwróconych
                $showUnreturned = null;
            else
                $showUnreturned = mysqli_real_escape_string($mysqli, ($_GET['unreturned']));


            if ($id_a && $id_k) { // jest podane id auta oraz id klienta czyli kiedy dany klient wypożyczył dane auto
                $id_a = mysqli_real_escape_string($mysqli, $_GET["id_a"]);
                $id_k = mysqli_real_escape_string($mysqli, $_GET["id_k"]);
                $sql_q = "$sql WHERE id_auta=? AND id_klienta=?";
                $stmt = $mysqli->prepare($sql_q);
                $stmt->bind_param("ii", $id_a, $id_k);
            } else if ($id_a && !$id_k) { //jest podane id auta ale nie id klienta, czyli wszystkie wypożyczenia danego auta
                $id_a = mysqli_real_escape_string($mysqli, $_GET["id_a"]);
                if ($showUnreturned)
                    $sql_q = "$sql WHERE id_auta=? AND data_zwrotu IS NULL";
                else
                    $sql_q = "$sql WHERE id_auta=?";
                $stmt = $mysqli->prepare($sql_q);
                $stmt->bind_param("i", $id_a);
            } else if (!$id_a && $id_k) { //jest podane id klienta ale nie id auta, czyli wszystkie wypożyczone auta danego klienta
                $id_k = mysqli_real_escape_string($mysqli, $_GET["id_k"]);
                if ($showUnreturned)
                    $sql_q = "$sql WHERE id_klienta=? AND data_zwrotu IS NULL"; //modyfikacja zapytań w zależności od filtra
                else
                    $sql_q = "$sql WHERE id_klienta=?";
                $stmt = $mysqli->prepare($sql_q);
                $stmt->bind_param("i", $id_k);
            } else { //nie zostało podane id klienta ani id auta więc wyszukiwane jest po zapytaniu zwyklym q
                if (!isset($_GET["q"]) || $_GET["q"] == "") { //nie ma zapytania
                    if ($showUnreturned)
                        $sql_q = "$sql WHERE data_zwrotu IS NULL ORDER BY id LIMIT ?";
                    else
                        $sql_q = "$sql ORDER BY wypozyczenia.id LIMIT ?";
                    $stmt = $mysqli->prepare($sql_q);
                    $stmt->bind_param("i", $limit);
                } else { // jest zapytanie więc przeszukiwane są dane typu marka model imie i nazwisko
                    $query = mysqli_real_escape_string($mysqli, $_GET["q"]);
                    if ($showUnreturned)
                        $sql_q = "$sql WHERE data_zwrotu IS NULL AND (LOWER(flota.marka) LIKE ? OR LOWER(flota.model) LIKE ? OR LOWER(klienci.imie) LIKE ? OR LOWER(klienci.nazwisko) LIKE ?) ORDER BY wypozyczenia.id LIMIT ?";
                    else
                        $sql_q = "$sql WHERE LOWER(flota.marka) LIKE ? OR LOWER(flota.model) LIKE ? OR LOWER(klienci.imie) LIKE ? OR LOWER(klienci.nazwisko) LIKE ? ORDER BY wypozyczenia.id LIMIT ?";
                    $stmt = $mysqli->prepare($sql_q);
                    $param = "%" . mb_strtolower($query) . "%";
                    $stmt->bind_param("ssssi", $param, $param, $param, $param, $limit);
                }
            }

            $stmt->execute();
            $results = $stmt->get_result();
            $data = $results->fetch_all(MYSQLI_ASSOC);

            if (count($data) == 0) {
                echo ("Brak wyników wyszukiwań.");
            }

            echo "<table id=\"offerTable\" cellspacing=\"0\">";
            echo "<thead><tr>
                <td id='id'>ID wypożyczenia</td>
                <td id='imie'>Imię</td>
                <td id='nazwisko'>Nazwisko</td>
                <td id='marka'>Marka</td>
                <td id='model'>Model</td>
                <td id='data_wypozyczenia'>Data wypożyczenia</td>
                <td id='data_zwrotu'>Data zwrotu</td>
                <td id='data_zwrotu'>Cena wypożyczenia</td>
                </tr></thead>";

            echo "<tbody>";
            foreach ($data as &$wypozyczenie) { //wyświetlanie wypożyczeń
                echo "<tr>";
                $id = htmlspecialchars($wypozyczenie["id"]);
                $imie = htmlspecialchars($wypozyczenie["imie"]);
                $nazwisko = htmlspecialchars($wypozyczenie["nazwisko"]);
                $id_klienta = htmlspecialchars($wypozyczenie["id_klienta"]);
                $marka = htmlspecialchars($wypozyczenie["marka"]);
                $model = htmlspecialchars($wypozyczenie["model"]);
                $id_auta = htmlspecialchars($wypozyczenie["id_auta"]);

                $data_wypozyczenia = htmlspecialchars($wypozyczenie["data_wypozyczenia"]);
                $data_zwrotu = htmlspecialchars($wypozyczenie["data_zwrotu"]);
                $cena = htmlspecialchars($wypozyczenie["cena"]);


                if ($data_zwrotu == "") { // jeśli nie ma daty zwrotu, to znaczy że nie zostało jeszcze zwrócone przez klienta
                    $data_zwrotu = "<a href=\"return.php?id=$id\">Zwróć samochód</a>"; // link do zwrócenia auta
                }


                echo "<td>$id</td>";
                echo "<td><a href=\"clients.php?q=$id_klienta\">$imie</a></td>"; // po kliknięciu w imie lub nazwisko wyświetlane są szczegółowe dane
                echo "<td><a href=\"clients.php?q=$id_klienta\">$nazwisko</a></td>";
                echo "<td><a href=\"offer.php?q=$id_auta\">$marka</a></td>";
                echo "<td><a href=\"offer.php?q=$id_auta\">$model</a></td>"; // analogicznie do auta
                echo "<td>$data_wypozyczenia</td>";
                echo "<td>$data_zwrotu</td>";
                echo "<td>$cena ZŁ</td>";

                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";


            ?>
        </div>

    </div>

</body>

</html>