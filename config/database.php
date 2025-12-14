<?php
// /config/database.php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_burudy";

// Menggunakan MySQLi Object-Oriented agar lebih mudah untuk Prepared Statements
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>