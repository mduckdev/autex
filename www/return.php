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
            require(dirname(__FILE__) . "/" . "./includes/csrf.php");

            if (!isset($_GET['id']) || !is_numeric($_GET['id']) || intval($_GET['id']) <= 0) { //sprawdzanie czy podano niezbędne parametry
                echo ("Nie podano id wypożyczenia");
                return;
            } else {
                $id = mysqli_real_escape_string($mysqli, intval($_GET['id']));
            }
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (!isset($_POST['id']) || !is_numeric($_POST['id']) || intval($_POST['id']) <= 0 || !isValidCSRF()) { //sprawdzanie czy podano niezbędne parametry po potwierdzeniu zwrotu
                    echo ("Nie podano id wypożyczenia");
                    return;
                } else {
                    $id = mysqli_real_escape_string($mysqli, intval($_POST['id']));
                }
            }

            $sql =
                "SELECT
                klienci.imie,klienci.nazwisko,klienci.nr_tel,klienci.email,
                flota.marka,flota.model,flota.nr_rej,flota.rocznik,flota.cena,
                wypozyczenia.data_wypozyczenia, wypozyczenia.id_auta
                FROM wypozyczenia
                JOIN klienci ON klienci.id = wypozyczenia.id_klienta
                JOIN flota on flota.id = wypozyczenia.id_auta
                WHERE wypozyczenia.id=? AND data_zwrotu IS NULL AND wypozyczenia.cena IS NULL
                "; // zapytanie do wyszukania danych o wypożyczeniu
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $results = $stmt->get_result();
            $data = $results->fetch_all(MYSQLI_ASSOC);
            if (count($data) == 0) { //złe id
                echo ("Nie ma takiego wypożyczenia.");
                return;
            }
            $wypozyczenie = $data[0];

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $sql =
                    "UPDATE wypozyczenia
                SET data_zwrotu = ?, cena = ?
                WHERE id = ? "; // akutalizacja rekordu wypożyczenia o cenę i datę zwrotu
                $data_wypozyczenia = htmlspecialchars($wypozyczenie["data_wypozyczenia"]);
                $data_zwrotu = date("Y-m-d", time()); //dzisiejsza data
                $cena = htmlspecialchars($wypozyczenie["cena"]);
                $dni = (time() - strtotime($data_wypozyczenia)) / (60 * 60 * 24); // ile dni upłynęło od wypożyczenia
                $cena_koncowa = ceil($cena * $dni); //cena wypożyczenia
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("sii", $data_zwrotu, $cena_koncowa, $id);
                $stmt->execute();
                $sql = "UPDATE flota
                SET dostepny = 1
                WHERE id = ? "; // zmiana statusu auta na z powrotem dostępne
                $id_auta = $wypozyczenie["id_auta"];
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("i",  $id_auta);
                $stmt->execute();
                header("Location: /autex/www/index.php");
            }
            //jeśli jednak nie był to formularz to wyświetlane jest podsumowanie
            $imie = htmlspecialchars($wypozyczenie["imie"]);
            $imie = ucfirst(mb_strtolower($imie));
            $nazwisko = htmlspecialchars($wypozyczenie["nazwisko"]);
            $nazwisko = ucfirst(mb_strtolower($nazwisko));
            $nr_tel = htmlspecialchars($wypozyczenie["nr_tel"]);
            $email = htmlspecialchars($wypozyczenie["email"]);



            $marka = htmlspecialchars($wypozyczenie["marka"]);
            $model = htmlspecialchars($wypozyczenie["model"]);
            $nr_rej = htmlspecialchars($wypozyczenie["nr_rej"]);
            $rocznik = htmlspecialchars($wypozyczenie["rocznik"]);

            $data_wypozyczenia = htmlspecialchars($wypozyczenie["data_wypozyczenia"]);
            $data_zwrotu = date("Y-m-d h:m:s", time());
            $cena = htmlspecialchars($wypozyczenie["cena"]);
            $dni = (time() - strtotime($data_wypozyczenia)) / (60 * 60 * 24);
            $cena_koncowa = ceil($cena * $dni);

            $csrf = $_SESSION['csrf_token'];
            $id = htmlspecialchars($id); //podsumowanie
            echo ("<div class=\"summary-container\">
                <h1>Podsumowanie Zwrotu Samochodu</h1>
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
                    <button id=\"wroc\">Wróć </button>
                    <form action=\"\" method=\"POST\" id =\"returnForm\" class=\"require-additional-confirm\">
                        <input type=\"hidden\" value=\"$csrf\" name=\"csrf_token\">
                        <input type=\"hidden\" value=\"$id\" name=\"id\">
                        <input type=\"submit\" value=\"Potwierdź zwrot\">
                    <form>
                </div>
            </div>");
            //formularz z potwierdzeniem  przesłania


            ?>

        </div>
    </div>

</body>

</html>