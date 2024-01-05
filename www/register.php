<?php
    require(dirname(__FILE__) ."/". "./includes/session.php");
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
    require(dirname(__FILE__) ."/". "./navbar.php");
    
    ?>
    <div id="formContainer">
        <form action="" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="text" name="firstName" id="firstName" placeholder="&#xf2c3; Imię (opcjonalnie)" style="font-family:Arial, FontAwesome">
            <input type="text" name="lastName" id="lastName" placeholder="&#xf2c3; Nazwisko (opcjonalnie)" style="font-family:Arial, FontAwesome">
            <input type="text" name="username" id="username" placeholder="&#xf2c0; Nazwa użytkownika (1-20 znaków)" style="font-family:Arial, FontAwesome">
            <input type="password" name="password" id="password" placeholder="&#xf023; Hasło (10-128 znaków)" style="font-family:Arial, FontAwesome">
            <input type="password" name="passwordRepeat" id="passwordRepeat" placeholder="&#xf023; Powtórz hasło" style="font-family:Arial, FontAwesome">
            <a href="login.php">Masz już konto?</a>
            <input type="submit" value="Zaloguj">

            <div id="info">
            <?php
              require(dirname(__FILE__) ."/". "./includes/csrf.php");
              if(!isValidCSRF()){
                  return;
              }

                
                if($_SERVER["REQUEST_METHOD"]!="POST"){
                    return;
                }

                $errors = false;
                
                if(!isset($_POST["username"]) || $_POST["username"] == "" 
                    || strlen($_POST["username"]) > 20
                    || strlen($_POST["username"]) < 1){
                        echo "<p>Nazwa użytkownika musi zawierać od 1 do 20 znaków.</p>";
                        $errors=true;
                }
                if(!isset($_POST["password"]) || $_POST["password"] == "" 
                    || strlen($_POST["password"]) > 128
                    || strlen($_POST["password"]) < 9){
                        echo "<p>Hasło musi zawierać od 10 do 128 znaków.</p>";
                        $errors=true;
                }
                if($_POST["password"] != $_POST["passwordRepeat"]){
                    echo "<p>Hasła się nie zgadzają.</p>";
                    $errors=true;
                }
                if($errors){
                    return;
                }

                require(dirname(__FILE__) ."/". "./includes/db.php");

                if(!isset($_POST["firstName"]) || strlen($_POST["firstName"])==0){
                    $firstName = mysqli_real_escape_string($mysqli,$_POST["firstName"]);
                }else{
                    $firstName = "NULL";
                }

                if(!isset($_POST["lastName"]) || strlen($_POST["lastName"])==0){
                    $lastName = mysqli_real_escape_string($mysqli,$_POST["lastName"]);
                }else{
                    $lastName = "NULL";
                }

                $username = mysqli_real_escape_string($mysqli,$_POST["username"]);
                $password = mysqli_real_escape_string($mysqli,$_POST["password"]);

                $sql = "SELECT * FROM uzytkownicy WHERE nazwa_uzytkownika = ?";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();
                $data = $result->fetch_all(MYSQLI_ASSOC);

                if(count($data) !=0){
                    echo("Nazwa użytkownika zajęta.");
                    return;
                }

                $passwordHash = password_hash($password,PASSWORD_DEFAULT);


                $sql = "INSERT INTO uzytkownicy(nazwa_uzytkownika,haslo,imie,nazwisko) VALUES (?,?,?,?)";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("ssss", $username,$passwordHash,$firstName,$lastName);
                $stmt->execute();
                header("Location: login.php");

            ?>
            </div>
            
        </form>
    </div>
   


</body>
</html>


