<?php
// /admin/config/config.php

// Database credentials
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');   // GANTI dengan username database Anda
define('DB_PASSWORD', '');       // GANTI dengan password database Anda
define('DB_NAME', 'db_burudy');  // GANTI dengan nama database yang Anda buat

// Membuat koneksi MySQLi
// Menggunakan sintaks procedural untuk penanganan error yang lebih eksplisit
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

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