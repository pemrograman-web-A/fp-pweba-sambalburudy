<?php
// /user/api/payment/get_token.php
session_start();
require '../../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    echo json_encode(['error' => 'Cart is empty or user not logged in']);
    exit;
}

// --- KONFIGURASI MIDTRANS ---
$serverKey = 'SB-Mid-server-XXXXXXXXXXXXXXXX'; // GANTI INI DENGAN SERVER KEY ANDA
$isProduction = false; // Ganti true jika sudah live
$apiUrl = $isProduction 
    ? 'https://app.midtrans.com/snap/v1/transactions' 
    : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

// --- 1. SIMPAN ORDER KE DATABASE SENDIRI ---
$userId = $_SESSION['user_id'];
$address = $_POST['address'];
$totalAmount = 0;
$orderItems = [];

foreach ($_SESSION['cart'] as $item) {
    $totalAmount += $item['price'] * $item['qty'];
    $orderItems[] = [
        'id' => $item['id'],
        'price' => $item['price'],
        'quantity' => $item['qty'],
        'name' => substr($item['name'], 0, 50) // Midtrans limit name length
    ];
}

// Insert Header Order
$stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status, shipping_address) VALUES (?, ?, 'pending', ?)");
$stmt->bind_param("ids", $userId, $totalAmount, $address);
$stmt->execute();
$orderId = $stmt->insert_id; // Dapatkan ID Order yang baru dibuat

// Insert Detail Items
$stmtItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)");
foreach ($_SESSION['cart'] as $item) {
    $stmtItem->bind_param("iiid", $orderId, $item['id'], $item['qty'], $item['price']);
    $stmtItem->execute();
}

// --- 2. REQUEST TOKEN KE MIDTRANS ---
$transaction_details = [
    'order_id' => 'BR-' . $orderId . '-' . time(), // Order ID Unik (Misal: BR-101-17092323)
    'gross_amount' => $totalAmount
];

$customer_details = [
    'first_name' => $_SESSION['name'],
    'email' => "user{$userId}@mail.com", // Sebaiknya ambil email asli dari DB users
];

$payload = [
    'transaction_details' => $transaction_details,
    'item_details'        => $orderItems,
    'customer_details'    => $customer_details
];

// Kirim CURL Request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'Authorization: Basic ' . base64_encode($serverKey . ':')
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 201) {
    $result = json_decode($response, true);
    
    // Kosongkan Keranjang setelah token didapat (Opsional, atau kosongkan setelah sukses bayar)
    // $_SESSION['cart'] = []; 
    
    echo json_encode(['token' => $result['token']]);
} else {
    echo json_encode(['error' => 'Midtrans Error: ' . $response]);
}
?>  