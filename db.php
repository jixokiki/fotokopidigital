<?php
$host = "localhost";
$user = "root";
$pass = "Bekasibarat12"; // Ganti dengan password MySQL kamu
$db   = "fotokopi_digital";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>