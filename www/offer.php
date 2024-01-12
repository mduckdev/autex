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
    <link rel="stylesheet" href="./css/offer.css">
    <script defer src="./js/search.js"></script>

    <title>Wypożyczalnia samochodów autex</title>
</head>

<body>
    <?php
    require("navbar.php");

    ?>

    <div class="transparent_background">
        <div class="offer">
            <form action="" method="get">
                <select name="limit" id="limit">
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="150">150</option>
                    <option value="200">200</option>
                </select>
                <input type="text" name="q" id="q" placeholder="Wyszukaj po ID,marce,modelu,kolorze lub nr rejestracyjnym">

                <input type="submit" value="Filtruj">
            </form>


            <div class="tableContainer">


                <?php
                require(dirname(__FILE__) . "/" . "./includes/db.php");



                if (!isset($_GET['limit']) || !is_numeric($_GET['limit']) || intval($_GET['limit']) <= 0 || intval($_GET['limit']) > 200)
                    $limit = 25;
                else
                    $limit = mysqli_real_escape_string($mysqli, intval($_GET['limit']));


                if (!isset($_GET["q"]) || $_GET["q"] == "") {
                    $sql = "SELECT * FROM flota LIMIT ?";
                    $stmt = $mysqli->prepare($sql);
                    $stmt->bind_param("i", $limit);
                } else {
                    $query = mysqli_real_escape_string($mysqli, $_GET["q"]);
                    $sql = "SELECT * FROM flota  WHERE id=? LIMIT ?";
                    $stmt = $mysqli->prepare($sql);
                    $stmt->bind_param("ii", $query, $limit);
                }


                $stmt->execute();
                $results = $stmt->get_result();
                $data = $results->fetch_all(MYSQLI_ASSOC);
                if (count($data) == 0) {
                    $query = mysqli_real_escape_string($mysqli, $_GET["q"]);
                    $sql = "SELECT * FROM flota  WHERE LOWER(marka) LIKE ? OR LOWER(model) LIKE ? OR LOWER(kolor) LIKE ? OR nr_rej=? LIMIT ?";
                    $stmt = $mysqli->prepare($sql);
                    $param = "%" . strtolower($query) . "%";
                    $stmt->bind_param("ssssi",  $param, $param, $param, $query, $limit);
                    $stmt->execute();
                    $results = $stmt->get_result();
                    $data = $results->fetch_all(MYSQLI_ASSOC);
                    if (count($data) == 0) {
                        echo ("Brak wyników wyszukiwań.");
                    }
                }



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
                </tr></thead>";

                echo "<tbody>";
                foreach ($data as &$auto) {
                    echo "<tr>";
                    $id = htmlspecialchars($auto["id"]);
                    $marka = htmlspecialchars($auto["marka"]);
                    $model = htmlspecialchars($auto["model"]);
                    $rocznik = htmlspecialchars($auto["rocznik"]);
                    $kolor = htmlspecialchars($auto["kolor"]);
                    $przebieg = htmlspecialchars($auto["przebieg"]);
                    $moc_km = htmlspecialchars($auto["moc_km"]);
                    $dostepnosc = ($auto["dostepny"] == 1) ? "<div class=\"greenText\">Tak</div>" : "<div class=\"redText\">Nie</div>";
                    $nr_rej = htmlspecialchars($auto["nr_rej"]);
                    $cena = htmlspecialchars($auto["cena"]);

                    echo "<td>$id</td>";
                    echo "<td>$marka</td>";
                    echo "<td>$model</td>";
                    echo "<td>$rocznik</td>";
                    echo "<td>$kolor</td>";
                    echo "<td>$przebieg</td>";
                    echo "<td>$moc_km</td>";
                    echo "<td>$dostepnosc</td>";
                    echo "<td>$nr_rej</td>";
                    echo "<td>$cena ZŁ</td>";
                    echo "<td><a href=\"rents.php?id_a=$id\">Historia wypożyczeń</a></td>";
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";


                ?>
            </div>

        </div>
    </div>


</body>

</html>