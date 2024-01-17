<?php
session_set_cookie_params([ //ustawienia ciasteczek sesji
    'lifetime' => 60 * 60 * 24, //24 godziny
    'path' => '/', // ścieżka pod którą mają być dostępne
    'domain' => 'localhost', //domena ciasteczek
    'secure' => true, // czy mają być wysyłane tylko szyfrowanym połączeniem (https)
    'httponly' => true, //ciasteczko dołączane jest tylko do zapytań przeglądarki, nie jest dostępne z poziomu js poprzez document.cookie
    'samesite' => 'Strict' //dodatkowe zabezpieczenie przed csrf, wymaganie Same Origin Policy do wszystkich przychodzących zapytań niezależnie od metody
]);
session_start(); // rozpoczęcie sesji

if (empty($_SESSION['csrf_token'])) { //jeśli token csrf nie był ustawiony to jest generowany, 32 pseudo-losowe bajty
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
