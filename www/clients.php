<?php
require(dirname(__FILE__) . "/" . "./includes/session.php");
include_once(dirname(__FILE__) . "/" . "./includes/auth.php");
requireAuth();
require(dirname(__FILE__) . "/" . "./includes/csp.php");

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


            <div class="tableContainer">


                <?php
                require(dirname(__FILE__) . "/" . "./includes/db.php");

                if (!isset($_GET['limit']) || !is_numeric($_GET['limit']) || intval($_GET['limit']) <= 0 || intval($_GET['limit']) > 1000)
                    $limit = 25;
                else
                    $limit = mysqli_real_escape_string($mysqli, intval($_GET['limit']));


                if (!isset($_GET["q"]) || $_GET["q"] == "") {
                    $sql = "SELECT * FROM klienci LIMIT ?";
                    $stmt = $mysqli->prepare($sql);
                    $stmt->bind_param("i", $limit);
                    $stmt->execute();
                    $results = $stmt->get_result();
                    $data = $results->fetch_all(MYSQLI_ASSOC);
                } else {
                    $sql = "SELECT * FROM klienci WHERE id=?";
                    $q = mysqli_real_escape_string($mysqli, $_GET["q"]);
                    $stmt = $mysqli->prepare($sql);
                    $stmt->bind_param("i", $q);
                    $stmt->execute();
                    $results = $stmt->get_result();
                    $data = $results->fetch_all(MYSQLI_ASSOC);
                }


                if (count($data) == 0) {
                    $sql = "SELECT * FROM klienci WHERE LOWER(imie) LIKE ? OR LOWER(nazwisko) LIKE ? OR LOWER(nr_tel) LIKE ? OR LOWER(email) LIKE ?";
                    $stmt = $mysqli->prepare($sql);
                    $param = "%" . strtolower($q) . "%";
                    $stmt->bind_param("ssss", $param, $param, $param, $param);
                    $stmt->execute();
                    $results = $stmt->get_result();
                    $data = $results->fetch_all(MYSQLI_ASSOC);
                }

                if (isset($_GET['rent']))
                    $col = "<td>Wybierz klienta który chce wypożyczyć</td>";
                else
                    $col = "";

                echo "<table id=\"offerTable\" cellspacing=\"0\">";
                echo "<thead><tr>
                <td id='id'>ID</td>
                <td id='imie'>Imię</td>
                <td id='nazwisko'>Nazwisko</td>
                <td id='nr_tel'>Nr. telefonu</td>
                <td id='email'>Adres e-mail</td>
                <td id='data_ur'>Data urodzenia</td>
                <td id='opcje'>Opcje</td>
                $col
                </tr></thead>";

                echo "<tbody>";
                foreach ($data as &$klient) {
                    echo "<tr>";
                    $id = htmlspecialchars($klient["id"]);
                    $imie = htmlspecialchars($klient["imie"]);
                    $nazwisko = htmlspecialchars($klient["nazwisko"]);
                    $nr_tel = htmlspecialchars($klient["nr_tel"]);
                    $email = htmlspecialchars($klient["email"]);
                    $data_ur = htmlspecialchars($klient["data_ur"]);

                    if (isset($_GET['rent']))
                        $col = "<td><a href=\"offer.php?id_k=$id&rent=on&onlyavailable=on\">Wybierz</a></td>";
                    else
                        $col = "";


                    echo "<td>$id</td>";
                    echo "<td>$imie</td>";
                    echo "<td>$nazwisko</td>";
                    echo "<td>$nr_tel</td>";
                    echo "<td>$email</td>";
                    echo "<td>$data_ur</td>";
                    echo "<td><a href=\"rents.php?id_k=$id\">Historia wypożyczeń</a></td>";
                    echo $col;
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