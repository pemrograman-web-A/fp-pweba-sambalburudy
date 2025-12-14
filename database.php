<?php
// /config/database.php

$host = getenv('MYSQLHOST') ?: "localhost";
$user = getenv('MYSQLUSER') ?: "root";
$pass = getenv('MYSQLPASSWORD') ?: "";
$db   = getenv('MYSQLDATABASE') ?: "db_burudy";
$port = getenv('MYSQLPORT') ?: 3306;

// Menggunakan MySQLi Object-Oriented agar lebih mudah untuk Prepared Statements
$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>

koneksi.php
<?php
require_once _DIR_ . '/config/database.php';
?>