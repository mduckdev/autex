<?php
require(dirname(__FILE__) . "/" . "./includes/session.php");
require(dirname(__FILE__) . "/" . "./includes/csp.php");
include_once(dirname(__FILE__) . "/" . "./includes/auth.php");
requireAuth();
requireEmployee();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autex</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/login.css">

</head>

<body>
    <?php
    require(dirname(__FILE__) . "/" . "./navbar.php");

    ?>
    <div id="formContainer">
        <form action="" method="post"> <!-- formularz do dodawania nowych klientów -->
        <?php
            require(dirname(__FILE__) . "/" . "./includes/db.php");
            if(!isset($_GET["id_k"])){
                return;
            }
            $id_k = mysqli_real_escape_string($mysqli,$_GET["id_k"]);

            $sql = "SELECT * FROM klienci WHERE id=?"; //zapytanie do bazy sprawdzające czy email lub nr telefonu nie jest zajęty
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $id_k);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_all(MYSQLI_ASSOC);
            $data = $data[0];
        
            if($_SERVER["REQUEST_METHOD"] == "GET"){
                $firstName = htmlspecialchars($data["imie"]);
                $lastName = htmlspecialchars($data["nazwisko"]);
                $phoneNumber = htmlspecialchars($data["nr_tel"]);
                $email = htmlspecialchars($data["email"]);
                $birthDate = htmlspecialchars($data["data_ur"]);
            }else{
                $firstName = htmlspecialchars($_POST["firstName"]);
                $lastName = htmlspecialchars($_POST["lastName"]);
                $phoneNumber = htmlspecialchars($_POST["phoneNumber"]);
                $email = htmlspecialchars($_POST["email"]);
                $birthDate = htmlspecialchars($_POST["birthDate"]);
            }
            
        ?>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="id_k" value="<?php echo htmlspecialchars($_GET["id_k"]); ?>">
            <input type="text" name="firstName" id="firstName" placeholder="&#xf2c3; Imię" value="<?php echo $firstName; ?>">
            <input type="text" name="lastName" id="lastName" placeholder="&#xf2c3; Nazwisko" value="<?php echo $lastName; ?>">
            <input type="tel" name="phoneNumber" id="phoneNumber" placeholder="&#xf095; Nr. telefonu 123456789" value="<?php echo $phoneNumber; ?>">
            <input type="text" name="email" id="email" placeholder="&#xf2b9; Adres e-mail" value="<?php echo $email; ?>">
            Data urodzenia
            <input type="date" name="birthDate" id="birthDate" placeholder="Data urodzenia" value="<?php echo $birthDate; ?>">
            <input type="submit" value="Zapisz">

            <div id="info">
                <?php
                require(dirname(__FILE__) . "/" . "./includes/csrf.php");
                if (!isValidCSRF()) {
                    return;
                }


                if ($_SERVER["REQUEST_METHOD"] != "POST") {
                    return;
                }

                $errors = false;

                if (
                    !isset($_POST["firstName"]) || $_POST["firstName"] == ""
                    || strlen($_POST["firstName"]) > 30
                    || strlen($_POST["firstName"]) < 1
                ) {
                    echo "<p>Imię musi zawierać od 1 do 30 znaków.</p>";
                    $errors = true;
                }

                if (
                    !isset($_POST["lastName"]) || $_POST["lastName"] == ""
                    || strlen($_POST["lastName"]) > 30
                    || strlen($_POST["lastName"]) < 1
                ) {
                    echo "<p>Nazwisko musi zawierać od 1 do 30 znaków.</p>";
                    $errors = true;
                }
                if (
                    !isset($_POST["phoneNumber"]) || $_POST["phoneNumber"] == ""
                    || strlen($_POST["phoneNumber"]) != 9 || !preg_match("/^\d{9}$/", $_POST["phoneNumber"]) // sprawdzanie wyrażeniem regularnym czy numer telefonu jest w prawidłowym formacie
                ) {
                    echo "<p>Numer telefonu musi składać się z 9 cyfr i być w formacie: 111555999</p>";
                    $errors = true;
                }
                if (
                    !isset($_POST["email"]) || $_POST["email"] == ""
                    || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) // w php jest dostępna funkcja do sprawdzenia czy string jest prawidłowym emailem
                ) {
                    echo "<p>Nieprawidłowy adres email</p>";
                    $errors = true;
                }
                if (
                    !isset($_POST["birthDate"]) || $_POST["birthDate"] == ""
                ) {
                    echo "<p>Data urodzenia jest wymagana.</p>"; // jeśli data urodzeń nie została wpisana wyświetlany jest ten błąd 
                    $errors = true;
                } else {
                    $today = new DateTime(); // w innym przypadku sprawdzany jest dodatkowo wiek klienta, minimum 18 lat
                    $birthDate = new DateTime($_POST["birthDate"]);
                    $difference = $birthDate->diff($today);
                    if ($difference->y < 18) {
                        echo "<p>Trzeba mieć 18 lat żeby być naszym klientem.</p>";
                        $errors = true;
                    }
                }


                if ($errors) { //jeśli któreś z pól formularza było nieprawidłowe skrypt jest przerywany
                    return;
                }

                require(dirname(__FILE__) . "/" . "./includes/db.php");


                $firstName = mysqli_real_escape_string($mysqli, $_POST["firstName"]); // zapisanie do zmiennych
                $firstName = mb_strtoupper($firstName);
                $lastName = mysqli_real_escape_string($mysqli, $_POST["lastName"]);
                $lastName = mb_strtoupper($lastName);

                $phoneNumber = mysqli_real_escape_string($mysqli, $_POST["phoneNumber"]);
                $email = mysqli_real_escape_string($mysqli, $_POST["email"]);
                $birthDate = mysqli_real_escape_string($mysqli, $_POST["birthDate"]);
                $id_k = mysqli_real_escape_string($mysqli, $_POST["id_k"]);


                $sql = "UPDATE klienci SET imie=?,nazwisko=?,nr_tel=?,email=?,data_ur=? WHERE id=?"; // jeśli są unikatowe dodawane są do bazy danych
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("ssissi", $firstName, $lastName, $phoneNumber, $email, $birthDate, $id_k);
                $stmt->execute();
                echo ("DODANO");
                header("Location: /autex/www/clients.php"); //przekierowanie do strony z listą klientów
                ?>
            </div>

        </form>
    </div>



</body>

</html>