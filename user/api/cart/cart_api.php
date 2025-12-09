<?php
// /user/api/cart/cart_api.php
session_start();
require '../../../config/database.php';

// Set Header JSON
header('Content-Type: application/json');

// Inisialisasi Cart jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $productId = $_POST['product_id'];

    // Cek apakah produk sudah ada di cart
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['qty'] += 1;
    } else {
        // Ambil detail produk dari DB
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $_SESSION['cart'][$productId] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'price' => $row['price'],
                'image' => $row['image'],
                'qty' => 1
            ];
        }
    }

    echo json_encode([
        'status' => 'success', 
        'message' => 'Added to cart',
        'cart_count' => count($_SESSION['cart'])
    ]);
    exit;
}

// Logic untuk update quantity atau remove bisa ditambahkan di sini nanti
?>