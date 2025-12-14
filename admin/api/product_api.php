<?php
// /admin/api/product/product_api.php

require_once '../config/config.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);

if (!$conn) { 
    http_response_code(500);
    echo json_encode(["message" => "Internal Server Error: Database Connection Failed."]);
    exit();
}

function generateNewId($conn) {
    $sql = "SELECT product_id FROM products ORDER BY product_id DESC LIMIT 1";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastNumber = (int)substr($row['product_id'], 2); 
        return 'BR' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    }
    return 'BR001';
}

switch ($method) {
    case 'GET':
        $sql = "SELECT product_id AS id, name, description, price, stock FROM products ORDER BY product_id ASC";
        $result = $conn->query($sql);
        $products = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['price'] = (float)$row['price'];
                $row['stock'] = (int)$row['stock'];
                $products[] = $row;
            }
        }
        echo json_encode($products);
        break;

    case 'POST':
        // CREATE
        if (empty($data['name']) || empty($data['price']) || !isset($data['stock'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Data produk tidak lengkap.']);
            break;
        }

        $newId = generateNewId($conn);
        $name = trim($data['name']);
        $description = $data['description'] ?? ''; // Default string kosong
        $price = (float)$data['price'];
        $stock = (int)$data['stock'];
        
        $sql = "INSERT INTO products (product_id, name, description, price, stock) VALUES (?, ?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            // --- PERBAIKAN DISINI ---
            // SALAH: "ssdii" (Description dianggap Double/Angka)
            // BENAR: "sssdi" 
            // Urutan: ID(s), Name(s), Description(s), Price(d), Stock(i)
            $stmt->bind_param("sssdi", $newId, $name, $description, $price, $stock);
            
            if ($stmt->execute()) {
                http_response_code(201);
                echo json_encode(['message' => 'Produk berhasil ditambahkan.', 'id' => $newId]);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Gagal insert: ' . $stmt->error]);
            }
            $stmt->close();
        }
        break;

    case 'PUT':
        // UPDATE
        $productId = $_GET['id'] ?? null;

        if (empty($productId) || empty($data['name']) || empty($data['price']) || !isset($data['stock'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Data tidak lengkap.']);
            break;
        }

        $name = trim($data['name']);
        $description = $data['description'] ?? '';
        $price = (float)$data['price'];
        $stock = (int)$data['stock'];

        $sql = "UPDATE products SET name = ?, description = ?, price = ?, stock = ? WHERE product_id = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            // --- PERBAIKAN DISINI ---
            // Pastikan formatnya: "ssdis"
            // Urutan: Name(s), Description(s), Price(d), Stock(i), ID(s)
            $stmt->bind_param("ssdis", $name, $description, $price, $stock, $productId);
            
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo json_encode(['message' => 'Produk berhasil diperbarui.']);
                } else {
                    // Cek apakah ID ada tapi data sama
                    $check = $conn->query("SELECT product_id FROM products WHERE product_id = '$productId'");
                    if ($check->num_rows > 0) {
                        echo json_encode(['message' => 'Data tersimpan (Tidak ada perubahan).']);
                    } else {
                        http_response_code(404);
                        echo json_encode(['message' => 'Produk tidak ditemukan.']);
                    }
                }
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Gagal update: ' . $stmt->error]);
            }
            $stmt->close();
        }
        break;

    case 'DELETE':
        $productId = $_GET['id'] ?? null;
        if (empty($productId)) {
            http_response_code(400);
            echo json_encode(['message' => 'ID produk tidak ada.']);
            break;
        }

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
                echo json_encode(['message' => 'Gagal delete: ' . $stmt->error]);
            }
            $stmt->close();
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['message' => 'Metode tidak diizinkan.']);
        break;
}

$conn->close();
?>