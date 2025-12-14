<?php
// /admin/api/product/product_api.php

// Muat konfigurasi database (Menggunakan MySQLi)
// Path disesuaikan: naik dua tingkat (dari /api/product/ ke /admin/) lalu masuk ke /config/
require_once '../config/config.php'; 

// Konfigurasi Header
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Menerima method request
$method = $_SERVER['REQUEST_METHOD'];
// Input JSON hanya relevan untuk POST dan PUT
$data = json_decode(file_get_contents("php://input"), true);

// Pastikan koneksi sukses (diambil dari config.php)
if (!isset($conn) || $conn->connect_error) { 
    http_response_code(500);
    echo json_encode(["message" => "Internal Server Error: Database Connection Failed (Fatal)."]);
    exit();
}

// --- Fungsi Pembantu SQL ---

function generateNewId($conn) {
    // Cari ID numerik tertinggi
    $sql = "SELECT product_id FROM products ORDER BY product_id DESC LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastId = $row['product_id']; // Contoh: BR003
        // Mengambil 3 digit terakhir dari ID
        $lastNumber = (int)substr($lastId, 2); 
        // Mengembalikan ID baru dengan padding '0'
        return 'BR' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    }
    return 'BR001';
}

// --- Logika Utama CRUD ---

switch ($method) {
    case 'GET':
        // READ: Mengambil semua data produk
        $sql = "SELECT product_id AS id, name, description, price, stock FROM products ORDER BY product_id ASC";
        $result = $conn->query($sql);
        $products = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Konversi string numerik menjadi integer/float untuk konsistensi JS
                $row['price'] = (float)$row['price'];
                $row['stock'] = (int)$row['stock'];
                $products[] = $row;
            }
        }
        http_response_code(200);
        echo json_encode($products);
        break;

    case 'POST':
        // CREATE: Menambah produk baru
        // Validasi minimal
        if (empty($data['name']) || empty($data['price']) || !isset($data['stock'])) {
            http_response_code(400); // Bad Request
            echo json_encode(['message' => 'Data produk (Nama, Harga, Stok) tidak lengkap.']);
            break;
        }

        $newId = generateNewId($conn);
        $name = trim($data['name']);
        $price = (float)$data['price'];
        $stock = (int)$data['stock'];
        $description = $data['description'] ?? null;
        
        $sql = "INSERT INTO products (product_id, name, description, price, stock) VALUES (?, ?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            // s: string, i: integer, d: double/float
            // Urutan parameter: product_id (s), name (s), description (s), price (d), stock (i)
            $stmt->bind_param("sssid", $newId, $name, $description, $price, $stock);
            
            if ($stmt->execute()) {
                http_response_code(201); // Created
                echo json_encode(['message' => 'Produk berhasil ditambahkan.', 'id' => $newId]);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Gagal menjalankan query: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Gagal menyiapkan statement: ' . $conn->error]);
        }
        break;

    case 'PUT':
        // UPDATE: Mengubah data produk
        $productId = $_GET['id'] ?? null;

        // Validasi minimal
        if (empty($productId) || empty($data['name']) || empty($data['price']) || !isset($data['stock'])) {
            http_response_code(400);
            echo json_encode(['message' => 'ID atau data produk (Nama, Harga, Stok) tidak lengkap.']);
            break;
        }

        $name = trim($data['name']);
        $price = (float)$data['price'];
        $stock = (int)$data['stock'];
        $description = $data['description'] ?? null;

        $sql = "UPDATE products SET name = ?, description = ?, price = ?, stock = ? WHERE product_id = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            // Perbaikan binding parameter:
            // Format: name (s), description (s), price (d), stock (i), product_id (s)
            // Tipe data: string, string, double/float, integer, string
            $stmt->bind_param("ssdis", $name, $description, $price, $stock, $productId);
            
            if ($stmt->execute()) {
                // Gunakan affected_rows untuk memastikan ada baris yang benar-benar diubah
                if ($stmt->affected_rows > 0) {
                    http_response_code(200);
                    echo json_encode(['message' => 'Produk berhasil diperbarui.', 'id' => $productId]);
                } else {
                    // Jika affected_rows == 0, ID mungkin tidak ada atau data yang dikirim sama
                    // Kita asumsikan 200 OK jika query berhasil tapi 0 affected, 
                    // kecuali kita ingin membedakan antara "Tidak Ditemukan" (404)
                    // Saya menggunakan 404 untuk kasus 'tidak ditemukan'
                    $checkSql = "SELECT product_id FROM products WHERE product_id = ?";
                    $checkStmt = $conn->prepare($checkSql);
                    $checkStmt->bind_param("s", $productId);
                    $checkStmt->execute();
                    
                    if ($checkStmt->get_result()->num_rows === 0) {
                         http_response_code(404);
                         echo json_encode(['message' => 'Produk tidak ditemukan.']);
                    } else {
                        // Produk ditemukan, tapi tidak ada perubahan data
                        http_response_code(200);
                        echo json_encode(['message' => 'Produk berhasil diperbarui (Tidak ada perubahan data).', 'id' => $productId]);
                    }
                    $checkStmt->close();
                }
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Gagal menjalankan query UPDATE: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Gagal menyiapkan statement: ' . $conn->error]);
        }
        break;

    case 'DELETE':
        // DELETE: Menghapus produk
        $productId = $_GET['id'] ?? null;
        
        if (empty($productId)) {
            http_response_code(400);
            echo json_encode(['message' => 'ID produk tidak ditentukan.']);
            break;
        }

        $sql = "DELETE FROM products WHERE product_id = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $productId);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    http_response_code(200);
                    echo json_encode(['message' => 'Produk berhasil dihapus.']);
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Produk tidak ditemukan.']);
                }
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Gagal menjalankan query DELETE: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Gagal menyiapkan statement: ' . $conn->error]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['message' => 'Metode tidak diizinkan.']);
        break;
}

// Tutup koneksi database
if (isset($conn)) {
    $conn->close();
}
?>