<?php
header('Content-Type: application/json');
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['greska' => 'Nije POST zahtjev']);
    exit;
}

$email = trim($_POST['email'] ?? '');
$lozinka = $_POST['lozinka'] ?? '';

// validacija
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['greska' => 'Email adresa nije ispravna']);
    exit;
}

if (strlen($lozinka) < 8) {
    echo json_encode(['greska' => 'Lozinka mora imati najmanje 8 znakova']);
    exit;
}

// provjeri postoji li već korisnik
$stmt = mysqli_prepare($conn, 'SELECT id FROM korisnik WHERE email = ?');
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    echo json_encode(['greska' => 'Korisnik s tim emailom već postoji']);
    exit;
}
mysqli_stmt_close($stmt);

// spremi korisnika
$hash = password_hash($lozinka, PASSWORD_BCRYPT);

$stmt = mysqli_prepare($conn, 'INSERT INTO korisnik (email, lozinka_hash) VALUES (?, ?)');
mysqli_stmt_bind_param($stmt, 'ss', $email, $hash);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['uspjeh' => true, 'poruka' => 'Registracija uspješna']);
} else {
    echo json_encode(['greska' => 'Greška pri registraciji']);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>