<?php
require(dirname(__FILE__) . "/" . "./includes/session.php");
include_once(dirname(__FILE__) . "/" . "./includes/auth.php");
requireAuth();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/car.ico" type="image/x-icon">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/offer.css">
    <title>Wypożyczalnia samochodów autex</title>
</head>

<body>
    <?php
    require("navbar.php");

    ?>
    <div class="transparent_background">
        <div class="offer">
            <form action="" method="get">
                <input type="text" name="id" id="id" placeholder="ID klienta">

                <input type="submit" value="Szukaj">
            </form>


            <div class="tableContainer">


                <?php
                require(dirname(__FILE__) . "/" . "./includes/db.php");
                $sql = "SELECT * FROM klienci WHERE id=?";


                if (!isset($_GET["id"]) || $_GET["id"] == "") {
                    echo ("Brak id użytkownika");
                } else {
                    $id = mysqli_real_escape_string($mysqli, $_GET["id"]);
                    $stmt = $mysqli->prepare($sql);
                    $stmt->bind_param("i", $id);
                }


                $stmt->execute();

                $results = $stmt->get_result();
                $data = $results->fetch_all(MYSQLI_ASSOC);

                if (count($data) == 0) {
                    echo ("Brak wyników wyszukiwań.");
                }

                echo "<table id=\"offerTable\" cellspacing=\"0\">";
                echo "<thead><tr>
                <td id='id'>ID</td>
                <td id='imie'>Imię</td>
                <td id='nazwisko'>Nazwisko</td>
                <td id='nr_tel'>Nr. telefonu</td>
                <td id='email'>Adres e-mail</td>
                <td id='data_ur'>Data urodzenia</td>
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


                    echo "<td>$id</td>";
                    echo "<td>$imie</td>";
                    echo "<td>$nazwisko</td>";
                    echo "<td>$nr_tel</td>";
                    echo "<td>$email</td>";
                    echo "<td>$data_ur</td>";
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