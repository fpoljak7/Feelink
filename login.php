<?php
header('Content-Type: application/json');
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['greska' => 'Nije POST zahtjev']);
    exit;
}

$email = trim($_POST['email'] ?? '');
$lozinka = $_POST['lozinka'] ?? '';

if (empty($email) || empty($lozinka)) {
    echo json_encode(['greska' => 'Unesite email i lozinku']);
    exit;
}

$stmt = mysqli_prepare($conn, 'SELECT id, lozinka_hash FROM korisnik WHERE email = ?');
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $korisnik_id, $lozinka_hash);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if (!$korisnik_id || !password_verify($lozinka, $lozinka_hash)) {
    echo json_encode(['greska' => 'Pogrešan email ili lozinka']);
    exit;
}

// spremi sesiju
$_SESSION['korisnik_id'] = $korisnik_id;

echo json_encode(['uspjeh' => true, 'poruka' => 'Prijava uspješna', 'korisnik_id' => $korisnik_id]);

mysqli_close($conn);
?>