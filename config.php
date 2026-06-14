<?php
define('DB_HOST', '127.0.0.1:3307');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'feelink');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die(json_encode(['greska' => 'Konekcija na bazu nije uspjela: ' . mysqli_connect_error()]));
}

mysqli_set_charset($conn, 'utf8mb4');
?>