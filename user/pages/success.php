<?php
// /user/pages/success.php
session_start();
require '../../config/database.php';

// Cek User
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: ../../login.php");
    exit;
}

$order_id = $_GET['order_id'] ?? '-';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil - Sambal Bu Rudy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { burudy: { red: "#B91C1C", gold: "#F59E0B", dark: "#1F2937", light: "#FEF2F2" } } } } };
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-50 font-sans text-burudy-dark min-h-screen flex items-center justify-center">

    <div class="max-w-md w-full px-6 py-12 text-center">
        <div class="mb-6 animate-bounce">
            <div class="w-24 h-24 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto text-4xl shadow-lg">
                <i class="fas fa-check"></i>
            </div>
        </div>

        <h1 class="text-3xl font-serif font-bold text-gray-800 mb-2">Pesanan Diterima!</h1>
        <p class="text-gray-500 mb-6">Terima kasih, pesanan Anda dengan ID <span class="font-bold text-burudy-red">#<?= htmlspecialchars($order_id) ?></span> telah kami catat.</p>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8">
            <h3 class="font-bold text-lg mb-2">Langkah Selanjutnya:</h3>
            <p class="text-sm text-gray-600 mb-4">
                1. Lakukan pembayaran sesuai nominal.<br>
                2. Upload bukti pembayaran di Google Form.<br>
                3. Tunggu verifikasi admin (max 24 jam).
            </p>
            
            <a href="#" onclick="openGForm()" class="text-burudy-red font-bold text-sm hover:underline">
                <i class="fas fa-external-link-alt"></i> Buka Ulang Form Upload Bukti
            </a>
        </div>

        <div class="space-y-3">
            <a href="home.php" class="block w-full bg-burudy-red text-white py-3 rounded-xl font-bold hover:bg-red-800 transition shadow-lg hover:shadow-xl">
                Kembali ke Dashboard
            </a>
            <a href="history.php" class="block w-full bg-white border border-gray-300 text-gray-700 py-3 rounded-xl font-bold hover:bg-gray-50 transition">
                Cek Riwayat Pesanan
            </a>
        </div>
    </div>

    <script>
        // Ambil URL GForm dari LocalStorage (atau hardcode jika mau)
        // Agar dinamis, kita set di checkout.php ke localStorage sebelum redirect
        function openGForm() {
            const url = localStorage.getItem('gform_url');
            if(url) window.open(url, '_blank');
            else alert('Link form tidak ditemukan, silakan hubungi admin.');
        }
    </script>
</body>
</html>