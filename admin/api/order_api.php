<?php

require_once '../config/config.php'; 

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$method = $_SERVER['REQUEST_METHOD'];

if (!$conn || $conn->connect_error) { 
    exit();
}

switch ($method) {
    case 'GET':
        // READ: Mengambil pesanan dengan status 'Menunggu Pembayaran' dan 'Perlu Konfirmasi Stok'
        $sql = "
            SELECT 
                o.order_id, 
                o.customer_name, 
                o.customer_phone, 
                o.total_amount, 
                o.status, 
                o.shipping_address,
                GROUP_CONCAT(CONCAT(od.quantity, 'x ', p.name) SEPARATOR ', ') AS item_details
            FROM orders o
            JOIN order_details od ON o.order_id = od.order_id
            JOIN products p ON od.product_id = p.product_id
            WHERE o.status IN ('Menunggu Pembayaran', 'Perlu Konfirmasi Stok')
            GROUP BY o.order_id
            ORDER BY o.order_date ASC
        ";
        $result = $conn->query($sql);
        $orders = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['total_amount'] = (float)$row['total_amount'];
                $orders[] = $row;
            }
        }
        echo json_encode($orders);
        break;

    case 'PUT':
        // UPDATE: Mengubah status pesanan
        $data = json_decode(file_get_contents("php://input"), true);
        $orderId = $_GET['id'] ?? null;
        $newStatus = $data['status'] ?? null;

        if (empty($orderId) || empty($newStatus)) {
            http_response_code(400);
            echo json_encode(['message' => 'ID pesanan atau status baru tidak lengkap.']);
            break;
        }

        // Contoh status yang valid: Diproses, Dibatalkan, Selesai
        $sql = "UPDATE orders SET status = ? WHERE order_id = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ss", $newStatus, $orderId);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    http_response_code(200);
                    echo json_encode(['message' => "Status pesanan #{$orderId} berhasil diubah menjadi {$newStatus}.", 'status' => $newStatus]);
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Pesanan tidak ditemukan atau status sudah sama.']);
                }
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Gagal menjalankan query UPDATE: ' . $stmt->error]);
            }
            $stmt->close();
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['message' => 'Metode tidak diizinkan.']);
        break;
}

if (isset($conn)) {
    $conn->close();
}
?>