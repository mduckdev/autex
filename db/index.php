<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generowanie danych w bazie</title>
</head>

<body>
    <script>
        if ((window.location.href.indexOf("?ok=1") <= -1)) {
            if (window.confirm("Czy na pewno chcesz usunąć rekordy z bazy danych i zastąpić je losowymi?")) { // potwierdzenie żeby przypadkowo wchodząc nie wykasować sobie bazy
                window.location.replace("./index.php?ok=1");
            }
        }
    </script>
    <?php
    if (!isset($_GET["ok"]))
        return;
    require("flota_gen.php");
    require("klienci_gen.php");
    require("wypozyczenia_gen.php"); // zaimportowanie trzech plików z funkcjami do generowania bazy danych
    require(dirname(__FILE__) . "/" . "../www/includes/db.php"); // zaimportowanie połączenia z bazą
    mysqli_set_charset($mysqli, "utf8");

    mysqli_execute_query($mysqli, "SET FOREIGN_KEY_CHECKS = 0;"); // zapytania sql do wyczyszczenia danych z bazy
    mysqli_execute_query($mysqli, "TRUNCATE wypozyczenia");
    mysqli_execute_query($mysqli, "TRUNCATE flota");
    mysqli_execute_query($mysqli, "TRUNCATE klienci");
    mysqli_execute_query($mysqli, "SET FOREIGN_KEY_CHECKS = 1;");

    flota_gen();
    klienci_gen();
    wypozyczenia_gen();
    $mysqli->close();
    ?>
</body>

</html>