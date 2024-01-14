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
    <link rel="stylesheet" href="./css/tables_search.css">
    <link rel="stylesheet" href="./css/return.css">

    <script defer src="./js/search.js"></script>
    <script defer src="./js/return.js"></script>

    <title>Wypożyczalnia samochodów autex</title>
</head>

<body>
    <?php
    require("navbar.php");

    ?>
    <div class="transparent_background">
        <div>
            <?php
            require(dirname(__FILE__) . "/" . "./includes/db.php");

            if (!isset($_GET['id_k']) || !is_numeric($_GET['id_k']) || intval($_GET['id_k']) <= 0) {
                echo ("Nie podano id klienta");
                return;
            } else {
                $id_k = mysqli_real_escape_string($mysqli, intval($_GET['id_k']));
            }
            if (!isset($_GET['id_a']) || !is_numeric($_GET['id_a']) || intval($_GET['id_a']) <= 0) {
                echo ("Nie podano id auta");
                return;
            } else {
                $id_a = mysqli_real_escape_string($mysqli, intval($_GET['id_a']));
            }

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                require(dirname(__FILE__) . "/" . "./includes/csrf.php");

                if (!isValidCSRF()) {
                    return;
                }
                if (!isset($_POST['id_k']) || !is_numeric($_POST['id_k']) || intval($_POST['id_k']) <= 0) {
                    echo ("Nie podano id klienta");
                    return;
                } else {
                    $id_k = mysqli_real_escape_string($mysqli, intval($_POST['id_k']));
                }
                if (!isset($_POST['id_a']) || !is_numeric($_POST['id_a']) || intval($_POST['id_a']) <= 0) {
                    echo ("Nie podano id auta");
                    return;
                } else {
                    $id_a = mysqli_real_escape_string($mysqli, intval($_POST['id_a']));
                }
            }



            $sql =
                "SELECT
                klienci.imie,klienci.nazwisko,klienci.nr_tel,klienci.email,
                flota.marka,flota.model,flota.nr_rej,flota.rocznik,flota.cena
                FROM klienci
                JOIN flota ON flota.id = ?
                WHERE klienci.id = ? AND flota.dostepny=1
                ";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ii", $id_a, $id_k);
            $stmt->execute();
            $results = $stmt->get_result();
            $data = $results->fetch_all(MYSQLI_ASSOC);
            if (count($data) == 0) {
                echo ("Nie ma takiego wypożyczenia.");
                return;
            }
            $rent_data = $data[0];
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $sql =
                    "INSERT INTO wypozyczenia(data_wypozyczenia,id_klienta,id_auta) VALUES (?,?,?)";
                $start_date = date("Y-m-d h:m:s", time());


                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("sii", $start_date, $id_k, $id_a);
                $stmt->execute();

                $sql = "UPDATE flota
                SET dostepny = 0
                WHERE id = ? ";

                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("i", $id_a);
                $stmt->execute();
                header("Location: /autex/www/index.php");
            }

            $imie = htmlspecialchars($rent_data["imie"]);
            $imie = ucfirst(mb_strtolower($imie));
            $nazwisko = htmlspecialchars($rent_data["nazwisko"]);
            $nazwisko = ucfirst(mb_strtolower($nazwisko));
            $nr_tel = htmlspecialchars($rent_data["nr_tel"]);
            $email = htmlspecialchars($rent_data["email"]);



            $marka = htmlspecialchars($rent_data["marka"]);
            $model = htmlspecialchars($rent_data["model"]);
            $nr_rej = htmlspecialchars($rent_data["nr_rej"]);
            $rocznik = htmlspecialchars($rent_data["rocznik"]);

            $data_wypozyczenia = date("Y-m-d h:m:s", time());
            $cena = htmlspecialchars($rent_data["cena"]);

            $csrf = $_SESSION["csrf_token"];
            $id_a = htmlspecialchars($id_a);
            $id_k = htmlspecialchars($id_k);

            echo ("<div class=\"summary-container\">
                <h1>Podsumowanie Wypożyczenia Samochodu</h1>
                <hr>
                <div class=\"section-header\">Dane Klienta</div>
                <div class=\"section-content\">
                    <p><strong>Imię:</strong> $imie</p>
                    <p><strong>Nazwisko:</strong> $nazwisko</p>
                    <p><strong>Numer Telefonu:</strong> $nr_tel</p>
                    <p><strong>Adres E-mail:</strong> $email</p>
                </div>
                <hr>
                <div class=\"section-header\">Dane Samochodu</div>
                <div class=\"section-content\">
                    <p><strong>Marka:</strong>$marka</p>
                    <p><strong>Model:</strong> $model</p>
                    <p><strong>Rok Produkcji:</strong> $rocznik</p>
                    <p><strong>Numer Rejestracyjny:</strong> $nr_rej</p>
                </div>
                <hr>
        
                <div class=\"section-header\">Koszty</div>
                <div class=\"section-content\">
                    <p><strong>Cena Wypożyczenia za dzień:</strong> $cena</p>
                    <p><strong>Data Rozpoczęcia Wypożyczenia:</strong> $data_wypozyczenia</p>
                </div>
                <hr>

                <div>
                    <button id=\"wroc\">Wróć </button>
                    
                    <form action=\"\" method=\"POST\" id =\"returnForm\" class=\"require-additional-confirm\">
                        <input type=\"hidden\" value=\"$csrf\" name=\"csrf_token\">
                        <input type=\"hidden\" value=\"$id_k\" name=\"id_k\">
                        <input type=\"hidden\" value=\"$id_a\" name=\"id_a\">

                        <input type=\"submit\" value=\"Potwierdź wypożyczenie\">
                    <form>
                </div>
            </div>");



            ?>

        </div>
    </div>

</body>

</html>