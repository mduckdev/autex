<?php
function isValidCSRF(){
    if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
        return true;
    } else {
        return false;
    }
}