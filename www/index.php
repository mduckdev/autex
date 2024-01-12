<?php
require(dirname(__FILE__) . "/" . "./includes/session.php");
require(dirname(__FILE__) . "/" . "./includes/csp.php");

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="./css/style.css">
    <title>Wypożyczalnia samochodów autex</title>
</head>

<body>
    <?php
    require("navbar.php");

    ?>

    <div class="transparent_background">
        <p id="slogan">
            Twój czas, twój wybór, twój Autex!
        </p>
        <div id="about_us">
            <p>
                Wypożyczalnia samochodów Autex to miejsce, gdzie mobilność spotyka się z wygodą, a podróżowanie staje się niezwykle łatwe i przyjemne. Naszym celem jest zapewnienie klientom elastyczności i swobody w podróżowaniu, dlatego oferujemy szeroką gamę nowoczesnych i dobrze utrzymanych pojazdów.
            </p>

            <p>
                W Autex dbamy o to, aby każda podróż była komfortowa i bezpieczna. Nasza flota obejmuje różnorodne modele, począwszy od ekonomicznych aut, poprzez rodzinne kombi, aż po luksusowe samochody. Bez względu na cel podróży - czy to służbowy wyjazd, rodzinne wakacje czy spontaniczny weekendowy wypad - Autex ma odpowiednią opcję dla Ciebie.
            </p>

        </div>

        <a href="offer.php">
            <div id="offer">ZOBACZ OFERTĘ</div>
        </a>
    </div>


</body>

</html>