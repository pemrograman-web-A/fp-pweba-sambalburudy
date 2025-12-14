<?php
// /admin/api/dashboard_api.php

// 1. Tampilkan Error (Untuk Debugging)
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once '../config/config.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

if (!isset($conn) || $conn->connect_error) {
    http_response_code(500);
    echo json_encode(["message" => "Database Connection Failed"]);
    exit();
}

// 2. ATUR TIMEZONE (PENTING AGAR 'HARI INI' AKURAT)
date_default_timezone_set('Asia/Jakarta');
$conn->query("SET time_zone = '+07:00'");

try {
    // --- A. PENDAPATAN HARI INI ---
    // MENGAMBIL DARI TABEL 'TRANSACTIONS' (BUKAN ORDERS)
    $sqlRevenue = "SELECT COALESCE(SUM(total_amount), 0) AS pendapatan_hari_ini 
                   FROM transactions 
                   WHERE status = 'COMPLETED' 
                   AND DATE(transaction_date) = CURDATE()";
    
    $resultRevenue = $conn->query($sqlRevenue);
    $rowRevenue = $resultRevenue->fetch_assoc();
    $pendapatanHariIni = (float)$rowRevenue['pendapatan_hari_ini'];

    // --- B. PESANAN BARU (24 JAM TERAKHIR) ---
    // Mengambil dari tabel 'orders' (yang statusnya pending)
    $sqlOrders = "SELECT COUNT(*) AS pesanan_baru 
                  FROM orders 
                  WHERE order_date >= (NOW() - INTERVAL 24 HOUR)
                  AND status NOT IN ('Selesai', 'Dibatalkan')";
    
    $resultOrders = $conn->query($sqlOrders);
    $rowOrders = $resultOrders->fetch_assoc();
    $pesananBaru = (int)$rowOrders['pesanan_baru'];

    // --- C. PRODUK STOK RENDAH ---
    $sqlStock = "SELECT COUNT(*) AS stok_rendah FROM products WHERE stock < 20";
    $resultStock = $conn->query($sqlStock);
    $rowStock = $resultStock->fetch_assoc();
    $stokRendah = (int)$rowStock['stok_rendah'];

    // --- D. REVIEW BARU / RATING ---
    // Menghitung rata-rata rating
    $sqlReview = "SELECT COALESCE(AVG(rating), 0) AS avg_rating FROM reviews";
    $resultReview = $conn->query($sqlReview);
    $rowReview = $resultReview->fetch_assoc();
    $avgRating = number_format((float)$rowReview['avg_rating'], 1); // 1 desimal (contoh: 4.5)

    // --- E. AKTIVITAS TERBARU ---
    $sqlActivity = "
        (SELECT 
            'order' as type, 
            CONCAT('Pesanan baru dari ', customer_name) as `desc`,
            CONCAT(FORMAT(total_amount, 0), ' IDR') as detail,
            order_date as waktu,
            'text-blue-600' as status_class
         FROM orders ORDER BY order_date DESC LIMIT 5)
        UNION ALL
        (SELECT 
            'review' as type,
            CONCAT('Ulasan bintang ', rating, ' untuk ', product_id) as `desc`,
            LEFT(comment, 30) as detail,
            review_date as waktu,
            'text-yellow-600' as status_class
         FROM reviews ORDER BY review_date DESC LIMIT 5)
        ORDER BY waktu DESC LIMIT 5
    ";

    $resultActivity = $conn->query($sqlActivity);
    $aktivitas = [];
    while ($row = $resultActivity->fetch_assoc()) {
        $time = strtotime($row['waktu']);
        $row['waktu'] = date('H:i', $time); 
        $aktivitas[] = $row;
    }

    // KIRIM RESPONSE JSON
    echo json_encode([
        'pendapatan_hari_ini' => $pendapatanHariIni,
        'pesanan_baru' => $pesananBaru,
        'produk_stok_rendah' => $stokRendah,
        'review_baru' => $avgRating, // Mengirim Rating Rata-rata
        'aktivitas_terbaru' => $aktivitas
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Server Error: ' . $e->getMessage()]);
}

$conn->close();
?>