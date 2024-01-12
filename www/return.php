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
    <link rel="stylesheet" href="./css/return.css">

    <script defer src="./js/search.js"></script>

    <title>Wypożyczalnia samochodów autex</title>
</head>

<body>
    <?php
    require("navbar.php");

    ?>
    <div class="transparent_background">
        <div class="offer">
            <?php
            require(dirname(__FILE__) . "/" . "./includes/db.php");
            if (!isset($_GET['id']) || !is_numeric($_GET['id']) || intval($_GET['id']) <= 0) {
                echo ("Nie podano id wypożyczenia");
                return;
            } else {
                $id = mysqli_real_escape_string($mysqli, intval($_GET['id']));
            }
            $sql =
                "SELECT
                klienci.imie,klienci.nazwisko,klienci.nr_tel,klienci.email,
                flota.marka,flota.model,flota.nr_rej,flota.rocznik,flota.cena,
                wypozyczenia.data_wypozyczenia
                FROM wypozyczenia
                JOIN klienci ON klienci.id = wypozyczenia.id_klienta
                JOIN flota on flota.id = wypozyczenia.id_auta
                WHERE wypozyczenia.id=?
                ";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $results = $stmt->get_result();
            $data = $results->fetch_all(MYSQLI_ASSOC);
            if (count($data) == 0) {
                echo ("Nie ma takiego wypożyczenia.");
                return;
            }
            $wypozyczenie = $data[0];
            $imie = htmlspecialchars($wypozyczenie["imie"]);
            $imie = ucfirst(strtolower($imie));
            $nazwisko = htmlspecialchars($wypozyczenie["nazwisko"]);
            $nazwisko = ucfirst(strtolower($nazwisko));
            $nr_tel = htmlspecialchars($wypozyczenie["nr_tel"]);
            $email = htmlspecialchars($wypozyczenie["email"]);



            $marka = htmlspecialchars($wypozyczenie["marka"]);
            $model = htmlspecialchars($wypozyczenie["model"]);
            $nr_rej = htmlspecialchars($wypozyczenie["nr_rej"]);
            $rocznik = htmlspecialchars($wypozyczenie["rocznik"]);

            $data_wypozyczenia = htmlspecialchars($wypozyczenie["data_wypozyczenia"]);
            $data_zwrotu = date("Y-m-d", time());
            $cena = htmlspecialchars($wypozyczenie["cena"]);
            $dni = (time() - strtotime($data_wypozyczenia)) / (60 * 60 * 24);
            $cena_koncowa = ceil($cena * $dni);

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
                    <p><strong>Data Zakończenia Wypożyczenia:</strong> $data_zwrotu</p>
                    <p><strong>Cena Całkowita:</strong> $cena_koncowa ZŁ</p>
                </div>
                <hr>

                <div>
                    <button >Wróć</button>
                    <button >Potwierdź Wypożyczenie</button>
                </div>
            </div>");



            ?>

        </div>
    </div>

</body>

</html>