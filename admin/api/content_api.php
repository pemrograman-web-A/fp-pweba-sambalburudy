<?php
// /admin/api/content/content_api.php

// Muat konfigurasi database
// Path disesuaikan: naik dua tingkat (dari /api/content/ ke /admin/) lalu masuk ke /config/
require_once '../../config/config.php';

// Konfigurasi Header
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, PUT"); // Hanya butuh GET (READ) dan PUT (UPDATE)
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Menerima method request
$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);

// Pastikan koneksi sukses
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["message" => "Internal Server Error: Database Connection Failed."]);
    exit();
}

switch ($method) {
    case 'GET':
        // READ: Mengambil semua data content_settings
        $sql = "SELECT content_key, content_value FROM content_settings";
        $result = $conn->query($sql);
        $content = [];

        if ($result && $result->num_rows > 0) {
            // Ubah data array asosiatif menjadi object key-value tunggal
            while ($row = $result->fetch_assoc()) {
                $content[$row['content_key']] = $row['content_value'];
            }
        }
        echo json_encode($content);
        break;

    case 'PUT':
        // UPDATE: Mengubah nilai konten
        if (isset($data['key']) && isset($data['value'])) {
            $key = $data['key'];
            $value = $data['value'];

            // Query: UPDATE atau INSERT jika key belum ada (UPSERT, tapi di sini pakai UPDATE)
            $sql = "UPDATE content_settings SET content_value = ? WHERE content_key = ?";
            
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("ss", $value, $key);
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        echo json_encode(['message' => "Konten key '{$key}' berhasil diperbarui."]);
                    } else {
                        // Jika tidak ada baris yang terpengaruh, cek apakah key itu ada
                        // Untuk simplicity, kita asumsikan key sudah di-INSERT awal.
                        http_response_code(404);
                        echo json_encode(['message' => "Konten key '{$key}' tidak ditemukan atau tidak ada perubahan data."]);
                    }
                } else {
                    http_response_code(500);
                    echo json_encode(['message' => 'Gagal menjalankan query UPDATE: ' . $stmt->error]);
                }
                $stmt->close();
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(['message' => 'Key atau Value konten tidak lengkap.']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['message' => 'Metode tidak diizinkan.']);
        break;
}

// Tutup koneksi database
$conn->close();
?>