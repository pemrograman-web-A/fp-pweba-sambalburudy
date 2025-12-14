<?php
// /user/pages/history.php
session_start();
require '../../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: ../../login.php");
    exit;
}

$uid = $_SESSION['user_id'];
// Ambil data order urut dari yang terbaru
$query = $conn->query("SELECT * FROM orders WHERE user_id = '$uid' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan - Sambal Bu Rudy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { burudy: { red: "#B91C1C", gold: "#F59E0B", dark: "#1F2937", light: "#FEF2F2" } } } } };
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-50 font-sans text-burudy-dark">

    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-4xl mx-auto px-4 h-20 flex justify-between items-center">
            <a href="home.php" class="flex items-center gap-2 text-gray-500 hover:text-burudy-red font-medium transition">
                <i class="fas fa-arrow-left"></i> Kembali ke Menu
            </a>
            <span class="font-serif text-xl font-bold">Riwayat Pesanan</span>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-4 py-10">
        <div class="space-y-6">
            <?php if ($query->num_rows > 0): ?>
                <?php while ($order = $query->fetch_assoc()): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-gray-100 gap-2">
                            <div>
                                <span class="text-gray-500 text-sm">Order ID: #BR-<?= $order['id'] ?></span>
                                <p class="text-xs text-gray-400"><?= date('d M Y H:i', strtotime($order['created_at'])) ?></p>
                            </div>
                            <?php
                                $statusClass = 'bg-gray-100 text-gray-600';
                                $label = ucfirst($order['status']);
                                if($order['status'] == 'paid') { $statusClass = 'bg-green-100 text-green-700'; $label = 'Lunas / Diproses'; }
                                elseif($order['status'] == 'pending') { $statusClass = 'bg-yellow-100 text-yellow-700'; $label = 'Menunggu Pembayaran'; }
                                elseif($order['status'] == 'shipped') { $statusClass = 'bg-blue-100 text-blue-700'; $label = 'Dikirim'; }
                                elseif($order['status'] == 'cancelled') { $statusClass = 'bg-red-100 text-red-700'; $label = 'Dibatalkan'; }
                            ?>
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase <?= $statusClass ?>">
                                <?= $label ?>
                            </span>
                        </div>
                        
                        <div class="p-6">
                            <div class="space-y-3 mb-4">
                                <?php
                                    // Ambil detail barang per order
                                    $oid = $order['id'];
                                    $items = $conn->query("
                                        SELECT oi.*, p.name, p.image 
                                        FROM order_items oi 
                                        JOIN products p ON oi.product_id = p.id 
                                        WHERE oi.order_id = '$oid'
                                    ");
                                    while($item = $items->fetch_assoc()):
                                ?>
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-gray-100 rounded-md overflow-hidden flex-shrink-0">
                                            <img src="../../images/<?= $item['image'] ?>" class="w-full h-full object-cover">
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-bold text-sm text-gray-800"><?= $item['name'] ?></h4>
                                            <p class="text-xs text-gray-500"><?= $item['quantity'] ?> x Rp <?= number_format($item['price_at_purchase'],0,',','.') ?></p>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                            
                            <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                                <span class="text-sm text-gray-600">Total Belanja</span>
                                <span class="text-xl font-bold text-burudy-red">Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></span>
                            </div>

                            <?php if($order['status'] == 'pending'): ?>
                                <div class="mt-4 text-right">
                                    <a href="checkout.php" class="text-sm bg-burudy-red text-white px-4 py-2 rounded-lg hover:bg-red-800 transition">Bayar Sekarang</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-12">
                    <p class="text-gray-500">Belum ada riwayat pesanan.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>