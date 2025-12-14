<?php
// /admin/api/dashboard_api.php

// Path: naik satu tingkat (..) lalu masuk ke folder config/
require_once '../config/config.php'; 

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$response = [];

try {
    // --- Data STATISTIK ---
    
    // A. Pesanan Baru (24 Jam Terakhir, status 'Menunggu Pembayaran')
    $stmt_orders = $conn->prepare("SELECT COUNT(order_id) FROM orders WHERE order_date >= DATE_SUB(NOW(), INTERVAL 24 HOUR) AND status = 'Menunggu Pembayaran'");
    $stmt_orders->execute();
    $stmt_orders->bind_result($new_orders);
    $stmt_orders->fetch();
    $stmt_orders->close();
    $response['pesanan_baru'] = $new_orders ?: 0;
    
    // B. Pendapatan Hari Ini
    $stmt_revenue = $conn->prepare("SELECT SUM(total_amount) FROM orders WHERE DATE(order_date) = CURDATE() AND status IN ('Dikirim', 'Selesai')");
    $stmt_revenue->execute();
    $stmt_revenue->bind_result($daily_revenue);
    $stmt_revenue->fetch();
    $stmt_revenue->close();
    $response['pendapatan_hari_ini'] = (float)$daily_revenue ?: 0;
    
    // C. Produk Stok Rendah (misalnya, stok < 20)
    // /admin/api/dashboard_api.php (Blok C. Produk Stok Rendah/Stok Terendah)

    // C. Produk Stok Terendah

    $stmt_min_stock = $conn->prepare("SELECT MIN(stock) AS stok_terendah FROM products");
    $min_stock = 0; // Nilai default

    if ($stmt_min_stock && $stmt_min_stock->execute()) {
        $stmt_min_stock->bind_result($stok_terendah);
        if ($stmt_min_stock->fetch()) {
            $min_stock = (int)$stok_terendah; // Ambil nilai stok terendah
        }
        $stmt_min_stock->close();
    }
    // Variabel yang dikirim ke frontend harus diganti ID-nya di admin.php

    // Catatan: Karena di frontend ID-nya adalah 'produk_stok_rendah',
    // kita kirim nilai MIN() ke ID tersebut.
    $response['produk_stok_rendah'] = $min_stock;

    // /admin/api/dashboard_api.php (Blok D. Review Baru)

   // D. Review Baru (Ganti dengan Rata-rata Rating)
    // Baris simulasi $response['review_baru'] = 12; telah dihapus

    // Query untuk menghitung rata-rata (Average) rating
    $stmt_avg_rating = $conn->prepare("SELECT AVG(rating), COUNT(review_id) FROM reviews");
    $avg_rating = 0.0;
    $total_reviews = 0;

    if ($stmt_avg_rating && $stmt_avg_rating->execute()) {
        $stmt_avg_rating->bind_result($avg_rating_result, $total_reviews_result);
        if ($stmt_avg_rating->fetch()) {
            $avg_rating = (float)$avg_rating_result ?: 0.0;
            $total_reviews = (int)$total_reviews_result ?: 0;
        }
        $stmt_avg_rating->close();
    }

    // Mengirim nilai rata-rata (dibulatkan 2 desimal)
    $response['avg_rating'] = number_format($avg_rating, 2); 
    $response['total_reviews'] = $total_reviews; 

    // INI PERUBAHAN UTAMA:
    // 'review_baru' (ID lama di frontend) sekarang akan menampilkan RATA-RATA RATING
    $response['review_baru'] = $response['avg_rating'];

} catch (Exception $e) {
    http_response_code(500);
    $response = ['error' => 'Gagal mengambil data: ' . $e->getMessage()];
}

if (isset($conn)) {
    $conn->close();
}

echo json_encode($response);
?>