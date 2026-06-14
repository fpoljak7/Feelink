<?php
header('Content-Type: application/json');
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['greska' => 'Nije POST zahtjev']);
    exit;
}

if (!isset($_SESSION['korisnik_id'])) {
    echo json_encode(['greska' => 'Nisi prijavljen']);
    exit;
}

$korisnik_id = $_SESSION['korisnik_id'];
$emoji = intval($_POST['emoji_vrijednost'] ?? 0);
$biljeska = trim($_POST['biljeska'] ?? '');

if ($emoji < 1 || $emoji > 5) {
    echo json_encode(['greska' => 'Nevažeća vrijednost raspoloženja']);
    exit;
}

// T08 — max jedan unos dnevno
$stmt = mysqli_prepare($conn, 
    'SELECT id FROM unos_raspolozenja 
     WHERE korisnik_id = ? AND DATE(datum_unosa) = CURDATE()');
mysqli_stmt_bind_param($stmt, 'i', $korisnik_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    echo json_encode(['greska' => 'Već si danas unio raspoloženje']);
    exit;
}
mysqli_stmt_close($stmt);

$stmt = mysqli_prepare($conn,
    'INSERT INTO unos_raspolozenja (korisnik_id, emoji_vrijednost, biljeska) VALUES (?, ?, ?)');
mysqli_stmt_bind_param($stmt, 'iis', $korisnik_id, $emoji, $biljeska);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['uspjeh' => true]);
} else {
    echo json_encode(['greska' => 'Greška pri spremanju']);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>