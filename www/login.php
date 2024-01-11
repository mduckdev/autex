<?php
require(dirname(__FILE__) . "/" . "../www/includes/session.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autex - logowanie</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/login.css">
    <script src="https://kit.fontawesome.com/258f783889.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php
    require(dirname(__FILE__) . "/" . "./navbar.php");

    ?>

    <div id="formContainer">
        <form action="" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="text" name="username" id="username" placeholder="&#xf2c0; Nazwa użytkownika" style="font-family:Arial, FontAwesome">
            <input type="password" name="password" id="password" placeholder="&#xf023; Hasło" style="font-family:Arial, FontAwesome">
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

                if (count($data) == 0) {
                    echo ($errorMessage);
                    return;
                }
                $userData = $data[0];
                if (!password_verify($password, $userData["haslo"])) {
                    echo ($errorMessage);
                    return;
                }
                if ($userData["aktywne"] == 0) {
                    echo ("Konto nie jest aktywne. Proszę skontaktować się z administratorem strony: <a href=\"mailto:email@example.com\">E-mail kontaktowy</a>");
                    return;
                }
                $_SESSION["loggedIn"] = 1;
                $_SESSION["userID"] = $userData["id"];
                $_SESSION["username"] = $userData["nazwa_uzytkownika"];

                header("Location: /autex/www/index.php");


                ?>
            </div>

        </form>

    </div>



</body>

</html>