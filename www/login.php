<?php
require(dirname(__FILE__) . "/" . "../www/includes/session.php");
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
        <form action="" method="post"><!-- formularz logowania -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="text" name="username" id="username" placeholder="&#61447; Nazwa użytkownika">
            <input type="password" name="password" id="password" placeholder="&#61475; Hasło">
            <a href="register.php">Nie masz konta?</a>
            <input type="submit" value="Zaloguj">
            <div id="info">
                <?php
                require(dirname(__FILE__) . "/" . "./includes/csrf.php");
                if (!isValidCSRF()) {
                    return;
                }

                if ($_SERVER["REQUEST_METHOD"] != "POST") {
                    return;
                }

                //weryfikacja danych
                if (!isset($_POST["username"]) || $_POST["username"] == "" || !isset($_POST["password"]) || $_POST["password"] == "") {
                    echo ("Wszystkie pola są wymagane.");
                    return;
                }


                require(dirname(__FILE__) . "/" . "../www/includes/db.php");

                $username = mysqli_real_escape_string($mysqli, $_POST["username"]);
                $password = mysqli_real_escape_string($mysqli, $_POST["password"]);

                $errorMessage = "Nazwa użytkownika lub hasło jest niepoprawne";

                $sql = "SELECT * FROM uzytkownicy WHERE nazwa_uzytkownika=?";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();
                $data = $result->fetch_all(MYSQLI_ASSOC);

                if (count($data) == 0) { //błąd jeśli nie ma takiego użytkownika
                    echo ($errorMessage);
                    return;
                }
                $userData = $data[0];
                if (!password_verify($password, $userData["haslo"])) { //sprawdzanie haszu hasła czy zgadza się z tym w bazie
                    echo ($errorMessage); //jeśli się nie zgadza wyświetlany jest ten sam komunikat o błędzie
                    return;
                }
                if ($userData["aktywne"] == 0) { // konta do korzystania z systemu muszą być wcześniej zatwierdzone przez kogoś z bezpośrednim dostępem do bazy danych, w bardziej realnym systemie byłby dostępny panel administracyjny do takich akcji (ale nie ma w tym projekcie)
                    echo ("Konto nie jest aktywne. Proszę skontaktować się z administratorem strony: <a href=\"mailto:email@example.com\">E-mail kontaktowy</a>"); // komunikat o błędzie żeby skontakować się z kimś technicznym
                    return;
                }
                $_SESSION["loggedIn"] = 1; // w przeciwnym wypadku ustanawiane są dane w sesji użytkownika 
                $_SESSION["userID"] = $userData["id"];
                $_SESSION["username"] = $userData["nazwa_uzytkownika"];

                header("Location: /autex/www/index.php"); //przekierowanie na główną stronę



                ?>
            </div>

        </form>

    </div>



</body>

</html>