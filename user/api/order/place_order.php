<?php
// user/api/order/place_order.php
session_start();
require '../../../config/database.php';

header('Content-Type: application/json');

// 1. Cek Login & Keranjang
if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    echo json_encode(['status' => 'error', 'message' => 'Keranjang kosong atau sesi habis.']);
    exit;
}

$userId = $_SESSION['user_id'];
$address = $_POST['address'] ?? '-';

// 2. Hitung Total
$totalAmount = 0;
foreach ($_SESSION['cart'] as $item) {
    $totalAmount += $item['price'] * $item['qty'];
}

// 3. Simpan ke Tabel Orders (Status: pending)
$stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status, shipping_address, created_at) VALUES (?, ?, 'pending', ?, NOW())");
$stmt->bind_param("ids", $userId, $totalAmount, $address);

if ($stmt->execute()) {
    $orderId = $stmt->insert_id;

    // 4. Simpan ke Tabel Order Items
    $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)");
    foreach ($_SESSION['cart'] as $item) {
        $stmtItem->bind_param("iiid", $orderId, $item['id'], $item['qty'], $item['price']);
        $stmtItem->execute();
    }

    // 5. Kosongkan Keranjang
    $_SESSION['cart'] = [];

    echo json_encode([
        'status' => 'success', 
        'message' => 'Pesanan berhasil dibuat', 
        'order_id' => $orderId
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan pesanan: ' . $conn->error]);
}
?>