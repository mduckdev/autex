<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/car.ico" type="image/x-icon">
    <link rel="stylesheet" href="./css/style.css">
    <title>Wypożyczalnia samochodów autex</title>
</head>
<body>
    <nav id="navbar">
        <a href="./index.php">
            <div id="navlogo">
                <img src="./img/car-512.png" alt="Autex logo">
                Autex
            </div>
        </a>
        
        <div id="navcontainer">
        <a href="#">Wypożycz samochód</a>
        <a href="#">Zwróć samochód</a>

            <a href="#">Wypożyczenia</a>
            <a href="#" id="last_item">Logowanie</a>
        </div>
    </nav>


    <div class="transparent_background">
        <div class="offer">
            <form action="" method="get">
                <select name="limit" id="limit">
                    <option value="10">10</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="150">150</option>
                    <option value="200">200</option>
                </select>
                <input type="submit" value="Filtruj">
            </form>


            <?php
                $mysqli = mysqli_connect("localhost", "root", "", "wypozyczalnia");
                mysqli_set_charset($mysqli, "utf8");
                
                if(!isset($_GET['limit']) || !is_numeric($_GET['limit']) || intval($_GET['limit'])<=0 || intval($_GET['limit'])>200)
                    $limit=25;
                else
                    $limit = mysqli_real_escape_string($mysqli,intval($_GET['limit']));

                //print_r($_GET['limit']);
                

                $stmt = $mysqli->prepare("SELECT * FROM flota LIMIT ?");
                $stmt->bind_param("i",$limit);
                $stmt->execute();
                
                $results = $stmt->get_result();
                $data = $results->fetch_all(MYSQLI_ASSOC);
                echo "<table id=\"offerTable\">";
                echo "<thead><tr>
                <td>ID</td>
                <td>Marka</td>
                <td>Model</td>
                <td>Rocznik</td>
                <td>Kolor</td>
                <td>Przebieg [Km]</td>
                <td>Moc [km]</td>
                </tr></thead>";

                echo "<tbody>";
                foreach($data as &$auto){
                    echo "<tr>";
                    $id= htmlspecialchars($auto["id"]);
                    $marka = htmlspecialchars($auto["marka"]);
                    $model = htmlspecialchars($auto["model"]);
                    $rocznik = htmlspecialchars($auto["rocznik"]);
                    $kolor = htmlspecialchars($auto["kolor"]);
                    $przebieg = htmlspecialchars($auto["przebieg"]);
                    $moc_km = htmlspecialchars($auto["moc_km"]);

                    echo "<td>$id</td>";
                    echo "<td>$marka</td>";
                    echo "<td>$model</td>";
                    echo "<td>$rocznik</td>";
                    echo "<td>$kolor</td>";
                    echo "<td>$przebieg</td>";
                    echo "<td>$moc_km</td>";

                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";


            ?>
        </div>
    </div>
</body>
</html>