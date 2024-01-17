<?php
require(dirname(__FILE__) . "/" . "./includes/session.php");
require(dirname(__FILE__) . "/" . "./includes/csp.php");
include_once(dirname(__FILE__) . "/" . "./includes/auth.php");
requireAuth();
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
            <form action="" method="get"> <!-- formularz wyszukiwania samochodów -->
                <input type="hidden" name="rent" value="<?php //sprawdza czy tryb wynajmu jest włączony i ustawia go tak jak był
                                                        if (!isset($_GET['rent']) || $_GET["rent"] != "on")
                                                            echo "off";
                                                        else
                                                            echo "on";
                                                        ?>">
                <?php
                if (isset($_GET['id_k']) && is_numeric($_GET['id_k'])) { // sprawdza czy jest w parametrze get id klienta (jest jeśli został wybrany do wynajmu i umieszcza go w ukrytym inpucie)
                    $id_k = htmlspecialchars($_GET['id_k']);
                    echo ("<input type=\"hidden\" name=\"id_k\" value=\"$id_k\">");
                }
                ?>

                <select name="limit" id="limit">
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="150">150</option>
                    <option value="200">200</option>
                </select>
                <input type="text" name="q" id="q" placeholder="Wyszukaj po ID,marce,modelu,kolorze lub nr rejestracyjnym">
                Tylko dostępne <input type="checkbox" name="onlyavailable" id="onlyavailable" <?php //zapamietywanie tego czy filtr tylko dostępnych był ustawiony
                                                                                                if (isset($_GET['onlyavailable']) && $_GET['onlyavailable'] == "on") {
                                                                                                    echo ("checked=\"true\"");
                                                                                                }
                                                                                                ?>>
                <input type="submit" value="Filtruj">
            </form>
            <a href="add_car.php"><button id="add_button">Dodaj nowy samochód</button></a> <!-- przycisk dodawania nowego samochodu -->
        </div>

        <div class="tableContainer">


            <?php
            require(dirname(__FILE__) . "/" . "./includes/db.php");

            if (!isset($_GET['limit']) || !is_numeric($_GET['limit']) || intval($_GET['limit']) <= 0 || intval($_GET['limit']) > 200)
                $limit = 25;
            else
                $limit = mysqli_real_escape_string($mysqli, intval($_GET['limit']));

            if (!isset($_GET['onlyavailable']) || $_GET['onlyavailable'] != "on")
                $onlyavailable = null;
            else
                $onlyavailable = mysqli_real_escape_string($mysqli, ($_GET['onlyavailable']));

            if (!isset($_GET["q"]) || $_GET["q"] == "") { //jeśli nie wyszukiwane jest nic
                $sql = "SELECT * FROM flota";
                if ($onlyavailable) //jeśli dodatkowo został włączony filtr dostępnych aut (dostępne auta mają pole dostepny ustawione na 1)
                    $sql_q = "$sql WHERE dostepny = 1 LIMIT ?";
                else
                    $sql_q = "$sql LIMIT ?";
                $stmt = $mysqli->prepare($sql_q);
                $stmt->bind_param("i", $limit);
            } else { // tutaj wiemy że q jest ustawione i ktoś coś wyszukał
                $query = mysqli_real_escape_string($mysqli, $_GET["q"]);
                $sql = "SELECT * FROM flota  WHERE id=?"; // podobnie jak wyszukując klientów najpierw stara się znaleźć po id auta
                if ($onlyavailable) // filtr dostępnych
                    $sql_q = "$sql AND dostepny = 1 LIMIT ?";
                else
                    $sql_q = "$sql LIMIT ?";
                $stmt = $mysqli->prepare($sql_q);
                $stmt->bind_param("ii", $query, $limit);
            }


            $stmt->execute();
            $results = $stmt->get_result();
            $data = $results->fetch_all(MYSQLI_ASSOC);
            if (count($data) == 0) { // jeśli jednak q to nie było id auta, wyszukiwane jest według innych parametrów
                $query = mysqli_real_escape_string($mysqli, $_GET["q"]);
                $sql = "SELECT * FROM flota WHERE( LOWER(marka) LIKE ? OR LOWER(model) LIKE ? OR LOWER(kolor) LIKE ? OR nr_rej=?) ";
                if ($onlyavailable)
                    $sql_q = "$sql AND dostepny = 1 LIMIT ?";
                else
                    $sql_q = "$sql LIMIT ?";
                $stmt = $mysqli->prepare($sql_q);
                $param = "%" . mb_strtolower($query) . "%";
                $stmt->bind_param("ssssi", $param, $param, $param, $query, $limit);
                $stmt->execute();
                $results = $stmt->get_result();
                $data = $results->fetch_all(MYSQLI_ASSOC);
                if (count($data) == 0) { // komunikat że nic nie znaleziono
                    echo ("Brak wyników wyszukiwań.");
                }
            }
            if (isset($_GET['rent']) && $_GET['rent'] == "on") // możliwość wybrania auta do wypożyczenia jeśli włączony jest tryb wynajmu
                $col = "<td>Wybierz auto do wypożyczenia</td>";
            else
                $col = "";


            echo "<table id=\"offerTable\" cellspacing=\"0\">";
            echo "<thead><tr>
                <td id='id'>ID</td>
                <td id='marka'>Marka</td>
                <td id='model'>Model</td>
                <td id='rocznik'>Rocznik</td>
                <td id='kolor'>Kolor</td>
                <td id='przebieg'>Przebieg [Km]</td>
                <td id='moc'>Moc [km]</td>
                <td id='dostepny'>Dostępny?</td>
                <td id='nr_rej'>Numer rejestracyjny</td>
                <td id='cena'>Cena [za dzień]</td>
                <td>Opcje</td>
                $col
                </tr></thead>";

            echo "<tbody>";
            foreach ($data as &$auto) { //wyświetlanie w tabeli wszystkich samochodów
                echo "<tr>";
                $id = htmlspecialchars($auto["id"]);
                $marka = htmlspecialchars($auto["marka"]);
                $model = htmlspecialchars($auto["model"]);
                $rocznik = htmlspecialchars($auto["rocznik"]);
                $kolor = htmlspecialchars($auto["kolor"]);
                $przebieg = htmlspecialchars($auto["przebieg"]);
                $moc_km = htmlspecialchars($auto["moc_km"]); // zależnie czy auto jest dostępne czy nie to ma inny kolor czcionki
                $dostepnosc = ($auto["dostepny"] == 1) ? "<div class=\"greenText\">Tak</div>" : "<div class=\"redText\">Nie</div>";
                $nr_rej = htmlspecialchars($auto["nr_rej"]);
                $cena = htmlspecialchars($auto["cena"]);
                //tutaj sprawdzane jest, czy włączony jest tryb wynajmu i czy id klienta jest wybrane, jeśli tak to wyświetlany jest przycisk do wybrania auta do wynajmu
                if (isset($_GET['rent']) && $_GET['rent'] == "on" && isset($_GET['id_k']) && is_numeric($_GET['id_k'])) {
                    $id_k = htmlspecialchars($_GET['id_k']);
                    $col = "<td><a href=\"rent_car.php?id_a=$id&id_k=$id_k\">Wybierz</a></td>";
                } else {
                    $col = "";
                }

                $csrf_token = $_SESSION['csrf_token'];


                echo "<td>$id</td>";
                echo "<td>$marka</td>";
                echo "<td>$model</td>";
                echo "<td>$rocznik</td>";
                echo "<td>$kolor</td>";
                echo "<td>$przebieg</td>";
                echo "<td>$moc_km</td>";
                echo "<td>$dostepnosc</td>";
                echo "<td>$nr_rej</td>";
                echo "<td>$cena ZŁ</td>"; // dane auta oraz historia wypożyczeń oraz formularz do usunięcia
                echo "<td class=\"options\">
                            <a href=\"rents.php?id_a=$id\">Historia wypożyczeń</a>
                            
                            <form action=\"delete.php\" method=\"POST\" class=\"require-additional-confirm delete-form\">
                                <input type=\"hidden\" name=\"csrf_token\" value=\"$csrf_token\">
                                <input type=\"hidden\" name=\"id_a\" value=\"$id\">
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