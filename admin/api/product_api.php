<?php
// /admin/api/product/product_api.php

// Muat konfigurasi database
// Path disesuaikan: naik dua tingkat (dari /api/product/ ke /admin/) lalu masuk ke /config/
require_once '../../config/config.php';

// Konfigurasi Header
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
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

// --- Fungsi Pembantu SQL ---

function generateNewId($conn) {
    // Cari ID numerik tertinggi
    $sql = "SELECT product_id FROM products ORDER BY product_id DESC LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastId = $row['product_id']; // Contoh: BR003
        $lastNumber = (int)substr($lastId, 2);
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
                // Konversi string numerik menjadi integer untuk konsistensi JS
                $row['price'] = (int)$row['price'];
                $row['stock'] = (int)$row['stock'];
                $products[] = $row;
            }
        }
        echo json_encode($products);
        break;

    case 'POST':
        // CREATE: Menambah produk baru
        if (isset($data['name']) && isset($data['price']) && isset($data['stock'])) {
            $newId = generateNewId($conn);
            $name = $data['name'];
            $price = (int)$data['price'];
            $stock = (int)$data['stock'];
            $description = $data['description'] ?? null;
            
            $sql = "INSERT INTO products (product_id, name, description, price, stock) VALUES (?, ?, ?, ?, ?)";
            
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("ssisi", $newId, $name, $description, $price, $stock);
                if ($stmt->execute()) {
                    http_response_code(201); // Created
                    echo json_encode(['message' => 'Produk berhasil ditambahkan.', 'id' => $newId]);
                } else {
                    http_response_code(500);
                    echo json_encode(['message' => 'Gagal menjalankan query: ' . $stmt->error]);
                }
                $stmt->close();
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(['message' => 'Data produk tidak lengkap.']);
        }
        break;

    case 'PUT':
        // UPDATE: Mengubah data produk
        $productId = $_GET['id'] ?? null;
        if ($productId && isset($data['name']) && isset($data['price']) && isset($data['stock'])) {
            $name = $data['name'];
            $price = (int)$data['price'];
            $stock = (int)$data['stock'];
            $description = $data['description'] ?? null;

            $sql = "UPDATE products SET name = ?, description = ?, price = ?, stock = ? WHERE product_id = ?";
            
            if ($stmt = $conn->prepare($sql)) {
                // s: string, i: integer
                $stmt->bind_param("sisis", $name, $description, $price, $stock, $productId);
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        echo json_encode(['message' => 'Produk berhasil diperbarui.', 'id' => $productId]);
                    } else {
                         http_response_code(404);
                         echo json_encode(['message' => 'Produk tidak ditemukan atau tidak ada perubahan data.']);
                    }
                } else {
                    http_response_code(500);
                    echo json_encode(['message' => 'Gagal menjalankan query UPDATE: ' . $stmt->error]);
                }
                $stmt->close();
            }
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'ID atau data produk tidak lengkap.']);
        }
        break;

    case 'DELETE':
        // DELETE: Menghapus produk
        $productId = $_GET['id'] ?? null;
        if ($productId) {
            $sql = "DELETE FROM products WHERE product_id = ?";
            
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("s", $productId);
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
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
            }
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'ID produk tidak ditentukan.']);
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