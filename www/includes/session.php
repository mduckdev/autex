<?php
session_set_cookie_params([
    'lifetime' => 60*60*24, 
    'path' => '/', 
    'domain' => 'localhost', 
    'secure' => false, 
    'httponly' => true, 
    'samesite' => 'Lax' // Powinno być 'Lax' lub 'Strict'
]);
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
