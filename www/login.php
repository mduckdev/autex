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
    require(dirname(__FILE__) ."/". "./navbar.php");
    
    ?>
    <?php
    //session_start();


    // if($_SERVER["REQUEST_METHOD"]=="POST"){
    //     $_SESSION['login'] = "1";
    //     print_r($_SESSION);
    // }

    ?>
    <div id="formContainer">
        <form action="" method="post">
            <input type="text" name="username" id="username" placeholder="Nazwa użytkownika">
            <input type="text" name="password" id="password" placeholder="Hasło">
            <a href="register.php">Nie masz konta?</a>
            <input type="submit" value="Zaloguj">
        </form>
    </div>
   


</body>
</html>


