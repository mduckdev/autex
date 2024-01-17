<?php
require(dirname(__FILE__) . "/" . "./includes/session.php"); //zaimportowanie plików związanych z sesją, csp oraz uwierzytelnieniem
require(dirname(__FILE__) . "/" . "./includes/csp.php");
include_once(dirname(__FILE__) . "/" . "./includes/auth.php");
requireAuth(); // ta funkcja przekierowuje do formularza logowania jeśli użytkownik nie jest zalogowany
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autex</title>
    <link rel="stylesheet" href="./css/style.css"> <!-- style i jsy -->
    <link rel="stylesheet" href="./css/login.css">

</head>

<body>
    <?php
    require(dirname(__FILE__) . "/" . "./navbar.php"); // wyświetlenie paska nawigacyjnego po stronie

    ?>
    <div id="formContainer">
        <form action="" method="post"> <!-- formularz do wypełnienia żeby dodać nowe auto do bazy, jeśli ktoś wypełnił go częściowo to jest uzupełniany -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"> <!-- do każdego formularza post jest dołączany token csrf-->
            <input type="text" name="brand" id="brand" placeholder="&#xf1b9; Marka" <?php if (isset($_POST["brand"])) echo ("value=\"" . htmlspecialchars($_POST["brand"])) . "\"" ?>>
            <input type="text" name="model" id="model" placeholder="&#xf1b9; Model" <?php if (isset($_POST["model"])) echo ("value=\"" . htmlspecialchars($_POST["model"])) . "\"" ?>>
            <input type="text" name="color" id="color" placeholder="&#xf1fc; Kolor" <?php if (isset($_POST["color"])) echo ("value=\"" . htmlspecialchars($_POST["color"])) . "\"" ?>>
            <input type="text" name="regNumber" id="regNumber" placeholder="&#xf2c3; Numer rejestracyjny" <?php if (isset($_POST["regNumber"])) echo ("value=\"" . htmlspecialchars($_POST["regNumber"])) . "\"" ?>>
            <input type="text" name="year" id="year" placeholder="&#xf073; Rocznik" <?php if (isset($_POST["year"])) echo ("value=\"" . htmlspecialchars($_POST["year"])) . "\"" ?>>
            <input type="text" name="mileage" id="mileage" placeholder="&#xf0e4; Przebieg [km]" <?php if (isset($_POST["mileage"])) echo ("value=\"" . htmlspecialchars($_POST["mileage"])) . "\"" ?>>
            <input type="text" name="power" id="power" placeholder="&#xf1b9; Moc [konie mechaniczne]" <?php if (isset($_POST["power"])) echo ("value=\"" . htmlspecialchars($_POST["power"])) . "\"" ?>>
            <input type="text" name="price" id="price" placeholder="&#xf155; Cena [za dzień]" <?php if (isset($_POST["price"])) echo ("value=\"" . htmlspecialchars($_POST["price"])) . "\"" ?>>
            <input type="submit" value="Dodaj auto">

            <div id="info">
                <?php
                require(dirname(__FILE__) . "/" . "./includes/csrf.php");
                if (!isValidCSRF()) { // jeśli token csrf się nie zgadza przerwij wykonywanie skryptu
                    return;
                }


                if ($_SERVER["REQUEST_METHOD"] != "POST") { // jeśli zapytanie nie zostało wysłane metodą post przerwij, głównie żeby można było wyświetlić za pierwszym razem bez błędów
                    return;
                }

                $errors = false; // zmienna do zarządzania czy wystąpił jakiś błąd, ustawiana na true jeśli któreś dane z formularza są nieprawidłowe

                if (
                    !isset($_POST["brand"]) || $_POST["brand"] == ""
                    || strlen($_POST["brand"]) > 30
                    || strlen($_POST["brand"]) < 1
                ) {
                    echo "<p>Marka musi zawierać od 1 do 30 znaków.</p>"; // komunikaty same tłumaczą wymagania 
                    $errors = true;
                }

                if (
                    !isset($_POST["model"]) || $_POST["model"] == ""
                    || strlen($_POST["model"]) > 30
                    || strlen($_POST["model"]) < 1
                ) {
                    echo "<p>Model musi zawierać od 1 do 30 znaków.</p>";
                    $errors = true;
                }

                if (
                    !isset($_POST["color"]) || $_POST["color"] == ""
                    || strlen($_POST["color"]) > 30
                    || strlen($_POST["color"]) < 1
                ) {
                    echo "<p>Kolor musi zawierać od 1 do 30 znaków.</p>";
                    $errors = true;
                }
                if (
                    !isset($_POST["regNumber"]) || $_POST["regNumber"] == ""
                    || strlen($_POST["regNumber"]) > 12
                    || strlen($_POST["regNumber"]) < 1
                ) {
                    echo "<p>Nr rejestracyjny musi zawierać od 1 do 12 znaków.</p>";
                    $errors = true;
                }

                if (
                    !isset($_POST["year"]) || $_POST["year"] == "" || !is_numeric($_POST["year"])
                    || intval($_POST["year"]) < 1900 //jakby ktoś wpisał jakiś dziwny/nieprawdopodobny rok, teoretycznie pierwszy samochód powstał w 1886 ale można to sobie darować
                ) {
                    echo "<p>Nieprawidłowy rocznik</p>";
                    $errors = true;
                }
                if (
                    !isset($_POST["mileage"]) || $_POST["mileage"] == "" || !is_numeric($_POST["mileage"])
                    || intval($_POST["mileage"]) < 0 //przebieg nie może być mniejszy od zera, musi być też liczbą
                ) {
                    echo "<p>Nieprawidłowy przebieg</p>";
                    $errors = true;
                }
                if (
                    !isset($_POST["power"]) || $_POST["power"] == "" || !is_numeric($_POST["power"])
                    || intval($_POST["power"]) < 0 //podobnie z mocą silnika
                ) {
                    echo "<p>Nieprawidłowa moc silnika</p>";
                    $errors = true;
                }
                if (
                    !isset($_POST["price"]) || $_POST["price"] == "" || !is_numeric($_POST["price"])
                    || intval($_POST["price"]) < 0 // cena też musi być liczbą >0
                ) {
                    echo "<p>Nieprawidłowa cena</p>";
                    $errors = true;
                }





                if ($errors) { // jeśli był jakiś błąd przerwij skrypt
                    return;
                }

                require(dirname(__FILE__) . "/" . "./includes/db.php"); // zaimportuj połączenie z bazą


                $brand = mysqli_real_escape_string($mysqli, $_POST["brand"]); // zapisanie przesłanych danych z formularza do zmiennych, użyta została funkcja mysqli_real_escape_string żeby zapobiec podatnościom SQL Injection
                $model = mysqli_real_escape_string($mysqli, $_POST["model"]);
                $color = mysqli_real_escape_string($mysqli, $_POST["color"]);
                $regNumber = mysqli_real_escape_string($mysqli, $_POST["regNumber"]);
                $regNumber = mb_strtoupper($regNumber);
                $year = mysqli_real_escape_string($mysqli, $_POST["year"]);
                $mileage = mysqli_real_escape_string($mysqli, $_POST["mileage"]);
                $power = mysqli_real_escape_string($mysqli, $_POST["power"]);
                $price = mysqli_real_escape_string($mysqli, $_POST["price"]);

                $sql = "INSERT INTO flota(marka,model,kolor,nr_rej,rocznik,przebieg,moc_km,cena) VALUES (?,?,?,?,?,?,?,?)"; // za pomocą prepared statement dodawany jest nowy samochód do bazy
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("ssssiiii", $brand, $model, $color, $regNumber, $year, $mileage, $power, $price);
                $stmt->execute();
                echo ("DODANO");
                header("Location: /autex/www/offer.php");
                ?>
            </div>

        </form>
    </div>



</body>

</html>