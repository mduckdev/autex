<?php
function isValidCSRF() //funkcja sprawdza czy ustawiony w sesji token zgadza się z przesłanym
{
    if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
        return true;
    } else {
        return false;
    }
}
