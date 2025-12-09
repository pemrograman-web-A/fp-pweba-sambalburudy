<?php
// /user/api/payment/callback.php
require '../../../config/database.php';

// Ambil data JSON mentah dari Midtrans
$json_result = file_get_contents('php://input');
$result = json_decode($json_result, true);

if ($result) {
    $notif_order_id = $result['order_id'];
    $transaction_status = $result['transaction_status'];
    $fraud_status = $result['fraud_status'];

    // Parsing ID Database dari Order ID Midtrans (Format: BR-{ID}-{TIMESTAMP})
    // Contoh: BR-15-16789999 -> Kita ambil angka '15'
    $parts = explode('-', $notif_order_id);
    $real_order_id = $parts[1];

    // Tentukan status baru untuk database
    $new_status = 'pending';

    if ($transaction_status == 'capture') {
        if ($fraud_status == 'challenge') {
            $new_status = 'pending';
        } else if ($fraud_status == 'accept') {
            $new_status = 'paid';
        }
    } else if ($transaction_status == 'settlement') {
        $new_status = 'paid'; // Uang sudah masuk / Lunas
    } else if ($transaction_status == 'cancel' || $transaction_status == 'deny' || $transaction_status == 'expire') {
        $new_status = 'cancelled';
    } else if ($transaction_status == 'pending') {
        $new_status = 'pending';
    }

    // Update Database
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $real_order_id);
    $stmt->execute();

    // Kirim respon OK ke Midtrans
    http_response_code(200);
} else {
    http_response_code(404);
}
?>