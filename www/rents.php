<?php
require(dirname(__FILE__) . "/" . "./includes/session.php");
include_once(dirname(__FILE__) . "/" . "./includes/auth.php");
requireAuth();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/car.ico" type="image/x-icon">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/offer.css">
    <title>Wypożyczalnia samochodów autex</title>
</head>

<body>
    <?php
    require("navbar.php");

    ?>
    <div class="transparent_background">
        <div class="offer">
            <form action="" method="get">
                <select name="limit" id="limit">
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="150">150</option>
                    <option value="200">200</option>
                    <option value="500">500</option>

                </select>
                <input type="text" name="q" id="q" placeholder="Imię,nazwisko, marka lub model">

                <input type="submit" value="Filtruj">
            </form>


            <div class="tableContainer">


                <?php
                require(dirname(__FILE__) . "/" . "./includes/db.php");
                $sql =
                    "SELECT wypozyczenia.id,wypozyczenia.id_klienta,wypozyczenia.id_auta,
                    klienci.imie,klienci.nazwisko,
                    flota.marka,flota.model,
                    wypozyczenia.data_wypozyczenia,wypozyczenia.data_zwrotu
                    FROM wypozyczenia
                    JOIN klienci ON klienci.id = wypozyczenia.id_klienta
                    JOIN flota on flota.id = wypozyczenia.id_auta
                    ORDER BY wypozyczenia.id";


                if (!isset($_GET['limit']) || !is_numeric($_GET['limit']) || intval($_GET['limit']) <= 0 || intval($_GET['limit']) > 600)
                    $limit = 25;
                else
                    $limit = mysqli_real_escape_string($mysqli, intval($_GET['limit']));


                if (!isset($_GET["q"]) || $_GET["q"] == "") {
                    $sql_q = "$sql LIMIT ?";
                    $stmt = $mysqli->prepare($sql_q);
                    $stmt->bind_param("i", $limit);
                } else {
                    $query = mysqli_real_escape_string($mysqli, $_GET["q"]);
                    $sql_q = "$sql WHERE LOWER(flota.marka) LIKE ? OR LOWER(flota.model) LIKE ? OR LOWER(klienci.imie) LIKE ? OR LOWER(klienci.nazwisko) LIKE ? LIMIT ?";
                    $stmt = $mysqli->prepare($sql_q);
                    $param = "%" . strtolower($query) . "%";
                    $stmt->bind_param("sssi", $param, $param, $param, $param, $limit);
                }


                $stmt->execute();

                $results = $stmt->get_result();
                $data = $results->fetch_all(MYSQLI_ASSOC);

                if (count($data) == 0) {
                    echo ("Brak wyników wyszukiwań.");
                }

                echo "<table id=\"offerTable\" cellspacing=\"0\">";
                echo "<thead><tr>
                <td id='id'>ID wypożyczenia</td>
                <td id='imie'>Imię</td>
                <td id='nazwisko'>Nazwisko</td>
                <td id='marka'>Marka</td>
                <td id='model'>Model</td>
                <td id='data_wypozyczenia'>Data wypożyczenia</td>
                <td id='data_zwrotu'>Data zwrotu</td>
                </tr></thead>";

                echo "<tbody>";
                foreach ($data as &$wypozyczenie) {
                    echo "<tr>";
                    $id = htmlspecialchars($wypozyczenie["id"]);
                    $imie = htmlspecialchars($wypozyczenie["imie"]);
                    $nazwisko = htmlspecialchars($wypozyczenie["nazwisko"]);
                    $id_klienta = htmlspecialchars($wypozyczenie["id_klienta"]);
                    $marka = htmlspecialchars($wypozyczenie["marka"]);
                    $model = htmlspecialchars($wypozyczenie["model"]);
                    $id_auta = htmlspecialchars($wypozyczenie["id_auta"]);

                    $data_wypozyczenia = htmlspecialchars($wypozyczenie["data_wypozyczenia"]);
                    $data_zwrotu = htmlspecialchars($wypozyczenie["data_zwrotu"]);


                    echo "<td>$id</td>";
                    echo "<td><a href=\"client_details.php?id=$id_klienta\">$imie</a></td>";
                    echo "<td><a href=\"client_details.php?id=$id_klienta\">$nazwisko</a></td>";
                    echo "<td><a href=\"client_details.php?id=$id_auta\">$marka</a></td>";
                    echo "<td><a href=\"client_details.php?id=$id_auta\">$model</a></td>";
                    echo "<td>$data_wypozyczenia</td>";
                    echo "<td>$data_zwrotu</td>";
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";


                ?>
            </div>

        </div>
    </div>
    <script>
        const input = document.getElementById("q");
        input.setAttribute('size', input.getAttribute('placeholder').length);


        sortTable = (index, order) => {
            const rows = Array.from(document.querySelectorAll("table#offerTable tbody tr"));
            const tbody = document.querySelector("table#offerTable tbody");
            let return1 = (order == "asc") ? -1 : 1;
            let return2 = (order == "asc") ? 1 : -1;

            rows.sort((a, b) => {
                let aCompare = a.childNodes[index].innerText;
                let bCompare = b.childNodes[index].innerText

                if (!isNaN(aCompare) && !isNaN(bCompare)) {
                    aCompare = Number(aCompare);
                    bCompare = Number(bCompare);
                }


                if (aCompare < bCompare)
                    return return1;
                if (aCompare > bCompare)
                    return return2;
                return 0;
            });

            tbody.innerHTML = "";

            rows.forEach(item => {
                tbody.appendChild(item);

            })



        }


        sort = (e) => {
            if (e) {
                const headers = document.querySelectorAll("table thead td");
                const clickedItem = e.target;
                const asc = "(ros.)";
                const desc = "(mal.)";

                const indexOfClickedItem = Array.prototype.indexOf.call(headers, clickedItem)

                headers.forEach(header => {
                    if (header == clickedItem) {
                        return;
                    }
                    header.innerText = header.innerText.replace(asc, "");
                    header.innerText = header.innerText.replace(desc, "");
                })

                if (!clickedItem.innerText.includes(asc) && !clickedItem.innerText.includes(desc)) {
                    clickedItem.innerText += ` ${asc}`;
                    sortTable(indexOfClickedItem, "asc");
                    return;
                }
                if (clickedItem.innerText.includes(asc)) {
                    clickedItem.innerText = clickedItem.innerText.replace(asc, "");
                    clickedItem.innerText += ` ${desc}`;
                    sortTable(indexOfClickedItem, "desc");
                    return;
                }
                if (clickedItem.innerText.includes(desc)) {
                    clickedItem.innerText = clickedItem.innerText.replace(desc, "");
                    clickedItem.innerText += ` ${asc}`;
                    sortTable(indexOfClickedItem, "asc");
                    return;
                }
            }



        }

        document.querySelectorAll("table thead td").forEach((item) => {
            item.addEventListener("click", sort);
        })
    </script>
</body>

</html>