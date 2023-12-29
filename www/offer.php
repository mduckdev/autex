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
                </select>
                <input type="text" name="q" id="q" placeholder="Wyszukaj po marce,modelu lub kolorze">

                <input type="submit" value="Filtruj">
            </form>


            <div class="tableContainer">


            <?php
            require(dirname(__FILE__) ."/". "../includes/db.php");

            if (!isset($_GET['limit']) || !is_numeric($_GET['limit']) || intval($_GET['limit']) <= 0 || intval($_GET['limit']) > 200)
                $limit = 25;
            else
                $limit = mysqli_real_escape_string($mysqli, intval($_GET['limit']));

            //print_r($_GET['limit']);
            if (!isset($_GET["q"]) || $_GET["q"] == "") {
                $sql = "SELECT * FROM flota LIMIT ?";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("i", $limit);
            } else {
                $query = mysqli_real_escape_string($mysqli, $_GET["q"]);
                $sql = "SELECT * FROM flota  WHERE MATCH(marka,model,kolor) AGAINST(? IN NATURAL LANGUAGE MODE) LIMIT ?";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("si", $query, $limit);
            }


            $stmt->execute();

            $results = $stmt->get_result();
            $data = $results->fetch_all(MYSQLI_ASSOC);

            if (count($data) == 0) {
                echo("Brak wyników wyszukiwań.");
                
            }

            echo "<table id=\"offerTable\" cellspacing=\"0\">";
            echo "<thead><tr>
                <td id='id'>ID</td>
                <td id='marka'>Marka</td>
                <td id='model'>Model</td>
                <td id='rocznik'>Rocznik</td>
                <td id='kolor'>Kolor</td>
                <td id='przebieg'>Przebieg [Km]</td>
                <td id='moc'>Moc [km]</td>
                <td id='dostepny'>Dostępny?</td>
                </tr></thead>";

            echo "<tbody>";
            foreach ($data as &$auto) {
                echo "<tr>";
                $id = htmlspecialchars($auto["id"]);
                $marka = htmlspecialchars($auto["marka"]);
                $model = htmlspecialchars($auto["model"]);
                $rocznik = htmlspecialchars($auto["rocznik"]);
                $kolor = htmlspecialchars($auto["kolor"]);
                $przebieg = htmlspecialchars($auto["przebieg"]);
                $moc_km = htmlspecialchars($auto["moc_km"]);
                $dostepnosc = ($auto["dostepny"] == 1) ? "<div style=\"color:green;\">Tak</div>" : "<div style=\"color:red;\">Nie</div>";


                echo "<td>$id</td>";
                echo "<td>$marka</td>";
                echo "<td>$model</td>";
                echo "<td>$rocznik</td>";
                echo "<td>$kolor</td>";
                echo "<td>$przebieg</td>";
                echo "<td>$moc_km</td>";
                echo "<td>$dostepnosc</td>";
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
        input.setAttribute('size',input.getAttribute('placeholder').length);


        sortTable = (index,order)=>{
            const rows = Array.from(document.querySelectorAll("table#offerTable tbody tr"));
            const tbody = document.querySelector("table#offerTable tbody");
            let return1 = (order=="asc")?-1:1;
            let return2 = (order=="asc")?1:-1;

            rows.sort((a,b)=>{
                let aCompare = a.childNodes[index].innerText;
                let bCompare = b.childNodes[index].innerText
                
                if(!isNaN(aCompare) && !isNaN(bCompare)){
                    aCompare = Number(aCompare);
                    bCompare = Number(bCompare);
                }


                if(aCompare<bCompare)
                    return return1;
                if(aCompare>bCompare)
                    return return2;
                return 0;
            });

            tbody.innerHTML = "";

            rows.forEach(item=>{
                tbody.appendChild(item);

            })
           


        }


        sort = (e) =>{
            if(e){
                const headers = document.querySelectorAll("table thead td");
                const clickedItem = e.target;
                const asc = "(ros.)";
                const desc = "(mal.)";

                const indexOfClickedItem = Array.prototype.indexOf.call(headers,clickedItem)

                headers.forEach(header =>{
                    if(header == clickedItem){
                        return;
                    }
                    header.innerText = header.innerText.replace(asc,"");
                    header.innerText = header.innerText.replace(desc,"");
                })

                if(!clickedItem.innerText.includes(asc) && !clickedItem.innerText.includes(desc)){
                    clickedItem.innerText += ` ${asc}`;
                    sortTable(indexOfClickedItem,"asc");
                    return;
                }
                if(clickedItem.innerText.includes(asc)){
                    clickedItem.innerText = clickedItem.innerText.replace(asc,"");
                    clickedItem.innerText += ` ${desc}`;
                    sortTable(indexOfClickedItem,"desc");
                    return;
                }
                if(clickedItem.innerText.includes(desc)){
                    clickedItem.innerText = clickedItem.innerText.replace(desc,"");
                    clickedItem.innerText += ` ${asc}`;
                    sortTable(indexOfClickedItem,"asc");
                    return;
                }
            }



        }

        document.querySelectorAll("table thead td").forEach((item) =>{
            item.addEventListener("click",sort);
        })

    </script>
</body>

</html>