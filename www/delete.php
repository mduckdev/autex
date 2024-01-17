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

    <title>Autex</title>
</head>

<body>
    <?php
    require("navbar.php");

    ?>
    <div class="transparent_background">
        <div>
            <?php
            require(dirname(__FILE__) . "/" . "./includes/db.php");

            if ($_SERVER["REQUEST_METHOD"] != "POST") {
                return;
            }

            require(dirname(__FILE__) . "/" . "./includes/csrf.php");

            if (!isValidCSRF()) {
                return;
            }
            $id_a = null;
            $id_k = null;
            if (isset($_POST['id_k']) && is_numeric($_POST['id_k']) && intval($_POST['id_k']) >= 0) { //sprawdzanie czy id klienta i id auta zostało przesłane, w ramach tego pliku można usuwać zarówno klientów oraz auta
                $id_k = mysqli_real_escape_string($mysqli, intval($_POST['id_k']));
            }

            if (isset($_POST['id_a']) && is_numeric($_POST['id_a']) && intval($_POST['id_a']) >= 0) {
                $id_a = mysqli_real_escape_string($mysqli, intval($_POST['id_a']));
            }

            if ($id_k && $id_a) { // tylko jedno na raz
                die("Można usunąć tylko jeden element w 1 zapytaniu.");
            }
            if ($id_k) { // usuwanie wypozyczen powiązanych z klientem
                $sql = "SELECT * FROM wypozyczenia WHERE id_klienta=? AND data_zwrotu IS NULL"; //zapytanie sprawdzające czy klient ma jakieś niezwrócone wypożyczenia
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("i", $id_k);
                $stmt->execute();
                $results = $stmt->get_result();
                $data = $results->fetch_all(MYSQLI_ASSOC);
                if (count($data) != 0) { // je
                    die("Nie można usunąć danych klienta który ma niezwrócone wypożyczenia."); //komunikat o błędzie
                }

                $sql = "DELETE FROM wypozyczenia WHERE id_klienta=?"; //w przeciwnym wypadku usuwa wszystkie jego wypożyczenia
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("i", $id_k);
                $stmt->execute();
                echo ("Usunięto wypożyczenia klienta o ID: " . htmlspecialchars($id_k));


                $sql = "DELETE FROM klienci WHERE id=?"; // oraz jego dane z bazy danych
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("i", $id_k);
                $stmt->execute();
                echo ("Usunięto klienta o ID: " . htmlspecialchars($id_k));
            }
            if ($id_a) { //analogiczna sytuacja z usuwaniem informacji o aucie, sprawdzanie czy jest aktualnie wypożyczone a następnie usuwanie o nim informacji
                $sql = "SELECT * FROM wypozyczenia WHERE id_auta=? AND data_zwrotu IS NULL";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("i", $id_a);
                $stmt->execute();
                $results = $stmt->get_result();
                $data = $results->fetch_all(MYSQLI_ASSOC);
                if (count($data) != 0) {
                    die("Nie można usunąć danych samochodu który jest aktualnie wypożyczony przez klienta.");
                }
                $sql = "DELETE FROM wypozyczenia WHERE id_auta=?";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("i", $id_a);
                $stmt->execute();
                echo ("Usunięto wypożyczenia powiązane z samochodem o ID: " . htmlspecialchars($id_a));

                $sql = "DELETE FROM flota WHERE id=?";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("i", $id_a);
                $stmt->execute();
                echo ("Usunięto samochód o ID: " . htmlspecialchars($id_a));
            }

            ?>

        </div>
    </div>

</body>

</html>