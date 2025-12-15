<?php
$host = getenv('MYSQLHOST') ?: "localhost";
$user = getenv('MYSQLUSER') ?: "root";
$pass = getenv('MYSQLPASSWORD') ?: "";
$db   = getenv('MYSQLDATABASE') ?: "db_burudy";
$port = getenv('MYSQLPORT') ?: 3306;

$conn = new mysqli($host, $user, $pass, $db, $port);

// Mengecek koneksi
if (mysqli_connect_errno()) {
    // 1. Menghentikan semua output header yang mungkin ada
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json; charset=UTF-8');
    }

    // 2. Memberikan pesan error yang jelas dan menghentikan eksekusi script
    echo json_encode([
        "error" => "Database Connection Failed",
        "message" => "Koneksi database gagal: " . mysqli_connect_error()
    ]);
    
    // Menghentikan script agar API tidak mencoba menggunakan variabel $conn yang gagal
    die(); 
}

// 3. Mengatur charset untuk mendukung Unicode (jika koneksi sukses)
if ($conn) {
    $conn->set_charset("utf8mb4");
}

// Variabel $conn sekarang siap digunakan oleh file API (seperti product_api.php)
?>