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
       
    <div id="offer">ZOBACZ OFERTĘ</div>
    </div>

    <script>
        const offer = document.getElementById("offer");
        offer.onclick=()=>{
            window.open("./offer.php","_blank");
        }
    </script>
</body>
</html>