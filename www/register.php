<?php
require(dirname(__FILE__) . "/" . "./includes/session.php");
require(dirname(__FILE__) . "/" . "./includes/csp.php");

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autex - logowanie</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/login.css">

</head>

<body>
    <?php
    require(dirname(__FILE__) . "/" . "./navbar.php");

    ?>
    <div id="formContainer">
        <form action="" method="post"> <!-- formularz rejestracji -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="text" name="email" id="email" placeholder="&#61447; Adres e-mail" style="font-family:Arial, FontAwesome">
            <input type="password" name="password" id="password" placeholder="&#xf023; Hasło (10-128 znaków)" style="font-family:Arial, FontAwesome">
            <input type="password" name="passwordRepeat" id="passwordRepeat" placeholder="&#xf023; Powtórz hasło" style="font-family:Arial, FontAwesome">
            <a href="login.php">Masz już konto?</a>
            <input type="submit" value="Zarejestruj">

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
                    !isset($_POST["email"]) 
                    || $_POST["email"] == ""
                    || strlen($_POST["email"]) > 100
                    || strlen($_POST["email"]) < 1
                    || !filter_var($_POST["email"],FILTER_VALIDATE_EMAIL)
                ) {
                    echo "<p>Nieprawidłowy adres email.</p>";
                    $errors = true;
                }
                if (
                    !isset($_POST["password"]) || $_POST["password"] == ""
                    || strlen($_POST["password"]) > 128
                    || strlen($_POST["password"]) < 9
                ) {
                    echo "<p>Hasło musi zawierać od 10 do 128 znaków.</p>";
                    $errors = true;
                }
                if ($_POST["password"] != $_POST["passwordRepeat"]) { //sprawdzane jest czy powtórzone hasło jest takie samo
                    echo "<p>Hasła się nie zgadzają.</p>";
                    $errors = true;
                }
                if ($errors) { //jeśli jakieś pola są błędne przerywany jest skrypt
                    return;
                }

                require(dirname(__FILE__) . "/" . "./includes/db.php");

                $email = mysqli_real_escape_string($mysqli, $_POST["email"]);
                $password = mysqli_real_escape_string($mysqli, $_POST["password"]);

                $sql = "SELECT * FROM uzytkownicy WHERE email = ?"; // sprawdzanie czy Adres e-mail nie jest zajęta
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                $data = $result->fetch_all(MYSQLI_ASSOC);

                if (count($data) != 0) {
                    echo ("Adres e-mail zajęty.");
                    return;
                }

                $passwordHash = password_hash($password, PASSWORD_DEFAULT); // do bazy danych zapisywany jest hasz hasła

                $sql = "INSERT INTO uzytkownicy(email,haslo) VALUES (?,?)";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("ss", $email, $passwordHash);
                $stmt->execute();

                $sql = "SELECT * FROM klienci WHERE email = ?"; // sprawdzanie czy email jest uzywany przez ktoregos z klientow
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                $data = $result->fetch_all(MYSQLI_ASSOC);

                if(count($data) == 0){
                    $sql = "INSERT INTO klienci(email) VALUES (?)";
                    $stmt = $mysqli->prepare($sql);
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                }
                $sql = "UPDATE uzytkownicy SET id_klienta = (SELECT id FROM klienci WHERE email = ?) WHERE email = ?";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("ss", $email, $email);
                $stmt->execute();

                header("Location: login.php"); // przekierowanie do formularza logowania

                ?>
            </div>

        </form>
    </div>



</body>

</html>