<?php
// /user/pages/cart.php
session_start();
require '../../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: ../../login.php");
    exit;
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Sambal Bu Rudy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { burudy: { red: "#B91C1C", gold: "#F59E0B", dark: "#1F2937", light: "#FEF2F2" } } } } };
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-50 font-sans text-burudy-dark">

    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 h-20 flex justify-between items-center">
            <a href="home.php" class="flex items-center gap-2 text-gray-500 hover:text-burudy-red font-medium transition">
                <i class="fas fa-arrow-left"></i> Kembali Belanja
            </a>
            <span class="font-serif text-xl font-bold">Keranjang Saya</span>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-10">
        <?php if (empty($cart)): ?>
            <div class="text-center py-20 bg-white rounded-2xl shadow-sm border border-gray-100">
                <i class="fas fa-shopping-basket text-6xl text-gray-200 mb-4"></i>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Keranjang Kosong</h2>
                <p class="text-gray-500 mb-6">Sepertinya Anda belum memilih oleh-oleh.</p>
                <a href="home.php" class="bg-burudy-red text-white px-6 py-3 rounded-full font-bold hover:bg-red-800 transition">Mulai Belanja</a>
            </div>
        <?php else: ?>
            <div class="flex flex-col lg:flex-row gap-8">
                <div class="lg:w-2/3">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <?php foreach ($cart as $id => $item): 
                            $subtotal = $item['price'] * $item['qty'];
                            $total += $subtotal;
                        ?>
                        <div class="p-6 border-b border-gray-100 flex items-center gap-4">
                            <div class="w-20 h-20 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                <img src="../../assets/images/<?= $item['image'] ?>" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-lg"><?= $item['name'] ?></h3>
                                <p class="text-burudy-red font-semibold">Rp <?= number_format($item['price'], 0, ',', '.') ?></p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-gray-600 font-medium">x<?= $item['qty'] ?></span>
                                <p class="font-bold text-lg w-28 text-right">Rp <?= number_format($subtotal, 0, ',', '.') ?></p>
                            </div>
                            <button class="text-gray-400 hover:text-red-600 px-2"><i class="fas fa-trash"></i></button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="lg:w-1/3">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                        <h3 class="font-bold text-xl mb-6">Ringkasan Belanja</h3>
                        <div class="flex justify-between mb-2 text-gray-600">
                            <span>Total Harga</span>
                            <span>Rp <?= number_format($total, 0, ',', '.') ?></span>
                        </div>
                        <div class="flex justify-between mb-6 text-burudy-red font-bold text-xl border-t pt-4">
                            <span>Total Bayar</span>
                            <span>Rp <?= number_format($total, 0, ',', '.') ?></span>
                        </div>
                        <a href="checkout.php" class="block w-full bg-burudy-red text-white text-center py-4 rounded-xl font-bold hover:bg-red-800 transition shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            Lanjut Checkout <i class="fas fa-chevron-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>