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
    require(dirname(__FILE__) . "/" . "./client_data_form.php");
    ?>
</body>

</html>