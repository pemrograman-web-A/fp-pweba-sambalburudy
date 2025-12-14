<?php
// /admin/config/config.php

// Database credentials
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');   // GANTI dengan username database Anda
define('DB_PASSWORD', '');       // GANTI dengan password database Anda
define('DB_NAME', 'db_burudy'); // GANTI dengan nama database yang Anda buat

// Membuat koneksi MySQLi
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Mengecek koneksi
if ($conn->connect_error) {
    // Jika koneksi gagal, hentikan eksekusi dan berikan pesan error JSON
    http_response_code(500);
    echo json_encode(["message" => "Koneksi database gagal: " . $conn->connect_error]);
    die();
}

// Mengatur charset untuk mendukung Unicode
$conn->set_charset("utf8mb4");
?>