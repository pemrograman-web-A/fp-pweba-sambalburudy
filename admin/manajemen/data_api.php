<?php
// Konfigurasi Header untuk CORS dan JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Lokasi "Database"
$dataFile = 'data.json';

// --- Fungsi Pembantu ---

function readProducts() {
    global $dataFile;
    if (!file_exists($dataFile)) {
        return [];
    }
    $json = file_get_contents($dataFile);
    return json_decode($json, true);
}

function writeProducts($products) {
    global $dataFile;
    $json = json_encode($products, JSON_PRETTY_PRINT);
    file_put_contents($dataFile, $json);
}

function generateNewId($products) {
    if (empty($products)) {
        return 'BR001';
    }
    // Ambil ID terakhir, BR003 -> 3
    $lastId = end($products)['id'];
    $lastNumber = (int)substr($lastId, 2);
    // Tambah 1, format menjadi 3 digit
    return 'BR' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
}

// --- Logika Utama CRUD ---

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);

$products = readProducts();

switch ($method) {
    case 'GET':
        // READ: Mengambil semua data produk
        echo json_encode($products);
        break;

    case 'POST':
        // CREATE: Menambah produk baru
        if (isset($data['name']) && isset($data['price']) && isset($data['stock'])) {
            $newProduct = [
                'id' => generateNewId($products),
                'name' => $data['name'],
                'price' => (int)$data['price'],
                'stock' => (int)$data['stock']
            ];
            $products[] = $newProduct;
            writeProducts($products);
            http_response_code(201); // Created
            echo json_encode(['message' => 'Produk berhasil ditambahkan.', 'data' => $newProduct]);
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(['message' => 'Data produk tidak lengkap.']);
        }
        break;

    case 'PUT':
        // UPDATE: Mengubah data produk (membutuhkan 'id' di URL parameter)
        if (isset($_GET['id']) && isset($data['name']) && isset($data['price']) && isset($data['stock'])) {
            $productId = $_GET['id'];
            $found = false;
            foreach ($products as $key => $product) {
                if ($product['id'] === $productId) {
                    $products[$key]['name'] = $data['name'];
                    $products[$key]['price'] = (int)$data['price'];
                    $products[$key]['stock'] = (int)$data['stock'];
                    $found = true;
                    writeProducts($products);
                    echo json_encode(['message' => 'Produk berhasil diperbarui.', 'data' => $products[$key]]);
                    break;
                }
            }
            if (!$found) {
                http_response_code(404); // Not Found
                echo json_encode(['message' => 'Produk tidak ditemukan.']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'ID atau data produk tidak lengkap.']);
        }
        break;

    case 'DELETE':
        // DELETE: Menghapus produk (membutuhkan 'id' di URL parameter)
        if (isset($_GET['id'])) {
            $productId = $_GET['id'];
            $initialCount = count($products);
            
            // Filter array untuk menghapus produk dengan ID yang cocok
            $products = array_filter($products, function($product) use ($productId) {
                return $product['id'] !== $productId;
            });

            if (count($products) < $initialCount) {
                writeProducts(array_values($products)); // array_values untuk mengatur ulang kunci array
                echo json_encode(['message' => 'Produk berhasil dihapus.']);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Produk tidak ditemukan.']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'ID produk tidak ditentukan.']);
        }
        break;

    default:
        http_response_code(405); // Method Not Allowed
        echo json_encode(['message' => 'Metode tidak diizinkan.']);
        break;
}
?>