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
    <script defer src="./js/delete.js"></script>

    <title>Wypożyczalnia samochodów autex</title>
</head>

<body>
    <?php
    require("navbar.php");

    ?>
    <div class="transparent_background">
        <div class="search_bar">
            <form action="" method="get"> <!-- formularz z wyszukiwaniem klientów, ukryty input z paremetrem rent jest odpowiedzialny za włączenie trybu wynajmowania, w którym można wybrać klienta do wypożyczenia, pokazywana jest dodatkowa kolumna -->
                <input type="hidden" name="rent" value="<?php
                                                        if (!isset($_GET['rent']))
                                                            echo "off";
                                                        else
                                                            echo "on";
                                                        ?>">
                <select name="limit" id="limit">
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="150">150</option>
                    <option value="200">200</option>
                    <option value="500">500</option>
                    <option value="1000">1000</option>

                </select>
                <input type="text" name="q" id="q" placeholder="ID klienta, imię, nazwisko, mail lub nr telefonu">

                <input type="submit" value="Szukaj">
            </form>
            <a href="add_client.php"><button id="add_button">Dodaj nowego klienta</button></a>


        </div>

        <div class="tableContainer">

            <?php
            require(dirname(__FILE__) . "/" . "./includes/db.php");
            //parametr limit odpowiada za to ile rekordów ma być wyświetlone
            if (!isset($_GET['limit']) || !is_numeric($_GET['limit']) || intval($_GET['limit']) <= 0 || intval($_GET['limit']) > 1000)
                $limit = 25;
            else
                $limit = mysqli_real_escape_string($mysqli, intval($_GET['limit'])); //zapisanie limitu do zmiennej


            if (!isset($_GET["q"]) || $_GET["q"] == "") { //jeśli nie wyszukano nic, pokazywane są wszystkie rekordy zgodnie z limitem
                $sql = "SELECT * FROM klienci LIMIT ?";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("i", $limit);
                $stmt->execute();
                $results = $stmt->get_result();
                $data = $results->fetch_all(MYSQLI_ASSOC);
            } else { //w innym przypadku wyszukuje najpierw po ID klienta
                $sql = "SELECT * FROM klienci WHERE id=?";
                $q = mysqli_real_escape_string($mysqli, $_GET["q"]);
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("i", $q);
                $stmt->execute();
                $results = $stmt->get_result();
                $data = $results->fetch_all(MYSQLI_ASSOC);
            }


            if (count($data) == 0) { // jeśli zapytanie nie było id, wyszukuje według innych kryteriów,imie,nazwisko,nr_tel lub email
                $sql = "SELECT * FROM klienci WHERE LOWER(imie) LIKE ? OR LOWER(nazwisko) LIKE ? OR LOWER(nr_tel) LIKE ? OR LOWER(email) LIKE ?";
                $stmt = $mysqli->prepare($sql);
                $param = "%" . mb_strtolower($q) . "%";
                $stmt->bind_param("ssss", $param, $param, $param, $param);
                $stmt->execute();
                $results = $stmt->get_result();
                $data = $results->fetch_all(MYSQLI_ASSOC);
            }

            if (isset($_GET['rent']) && $_GET['rent']=="on") // jeśli włączony jest tryb wypożyczania dodatkowa kolumna umożliwia wybór klienta
                $col = "<td>Wybierz klienta który chce wypożyczyć</td>";
            else
                $col = "";

            echo "<table id=\"offerTable\" cellspacing=\"0\">"; // wyświetlanie tabeli z wynikami
            echo "<thead><tr>
                <td id='id'>ID</td>
                <td id='imie'>Imię</td>
                <td id='nazwisko'>Nazwisko</td>
                <td id='nr_tel'>Nr. telefonu</td>
                <td id='email'>Adres e-mail</td>
                <td id='data_ur'>Data urodzenia</td>
                <td id='opcje'>Opcje</td>
                $col
                </tr></thead>"; // zmienna col zawiera potencjalnie dodatkową kolumnę, chyba że nie jest ustawiony parametr rent, wtedy nie ma dodatkowej kolumny

            echo "<tbody>";
            foreach ($data as &$klient) {
                echo "<tr>";
                $id = htmlspecialchars($klient["id"]);
                $imie = htmlspecialchars($klient["imie"]);
                $nazwisko = htmlspecialchars($klient["nazwisko"]);
                $nr_tel = htmlspecialchars($klient["nr_tel"]);
                $email = htmlspecialchars($klient["email"]);
                $data_ur = htmlspecialchars($klient["data_ur"]);

                if (isset($_GET['rent']) && $_GET['rent']=="on")
                    $col = "<td><a href=\"offer.php?id_k=$id&rent=on&onlyavailable=on\">Wybierz</a></td>"; //jeśli tryb wynajmu jest włączony wyświetlany jest link do pliku offer php z id wybranego klienta, włączonym trybem wynajmu oraz filtrem żeby pokazane zostały tylko dostępne auta
                else
                    $col = "";

                $csrf_token = $_SESSION["csrf_token"];
                echo "<td>$id</td>";
                echo "<td>$imie</td>";
                echo "<td>$nazwisko</td>";
                echo "<td>$nr_tel</td>";
                echo "<td>$email</td>";
                echo "<td>$data_ur</td>"; //wyświetlanie danych klientów, w ostatniej kolumnie jest link do historii wypożyczeń klienta oraz przycisk do usunięcia jego danych z bazy 
                echo "<td>
                            <a href=\"rents.php?id_k=$id\">Historia wypożyczeń</a> <br>
                            <a href=\"edit_client.php?id_k=$id\">Edytuj</a>
                            <form action=\"delete.php\" method=\"POST\" id=\"\" class=\"require-additional-confirm delete-form\">
                                <input type=\"hidden\" name=\"csrf_token\" value=\"$csrf_token\">
                                <input type=\"hidden\" name=\"id_k\" value=\"$id\">
                                <input class=\"delete-button\" type=\"submit\"value=\"USUŃ\">   
                            </form>
                        </td>";
                echo $col;
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";


            ?>
        </div>

    </div>


</body>

</html>