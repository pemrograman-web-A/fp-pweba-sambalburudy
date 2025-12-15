<?php
// Mengambil nilai dari variabel lingkungan
$host = getenv('MYSQLHOST') ?: "localhost";
$user = getenv('MYSQLUSER') ?: "root";
$pass = getenv('MYSQLPASSWORD') ?: "";
$db   = getenv('MYSQLDATABASE') ?: "db_burudy";
$port = getenv('MYSQLPORT') ?: 3306;

// Menyiapkan DSN untuk PDO
$dsn = "mysql:host=$host;dbname=$db;port=$port";
try {
    // Membuat koneksi menggunakan PDO
    $conn = new PDO($dsn, $user, $pass);
    // Menyeting mode error ke exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully"; 
} catch (PDOException $e) {
    die("Nathan ganteng: " . $e->getMessage());
}
?>