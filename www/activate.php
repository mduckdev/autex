<?php
    function getDomainFromEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
           return false;
        } 
        $list = explode('@', $email);
        
        if(count($list) !==2){
            return false;
        }
        return $list[1];
    }
    require(dirname(__FILE__) . "/" . "./includes/db.php"); 
    if(!isset($_GET["code"])){
        header("Location: index.php");
        return;
    }
    $code = mysqli_real_escape_string($mysqli,$_GET["code"]);
    $code_hashed = hash("sha256",$code);
    $timestamp =  date("Y-m-d h:m:s", time());
    $sql = "SELECT * FROM uzytkownicy WHERE kod_aktywacyjny=? AND kod_waznosc>? AND aktywne=0"; 
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ss", $code_hashed, $timestamp);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows===0){
        header("Location: index.php");
        exit();
    }else{
        $data = $result->fetch_assoc();
        $user_id = mysqli_real_escape_string($mysqli, $data["id"]);
        $isEmployee = 0;
        if(getDomainFromEmail($data["email"]) === "autex.com"){
            $isEmployee=1;
        }
        $sql = "UPDATE uzytkownicy SET aktywne=1, pracownik=? WHERE id=?"; 
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ii", $isEmployee, $user_id);
        $stmt->execute();
        header("Location: index.php");
        exit();
    }
?>