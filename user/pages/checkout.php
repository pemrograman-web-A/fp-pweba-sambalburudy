<?php
// /user/pages/checkout.php
session_start();
require '../../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user' || empty($_SESSION['cart'])) {
    header("Location: home.php");
    exit;
}

// Ambil Data User untuk Pre-fill form
$uid = $_SESSION['user_id'];
$query = $conn->query("SELECT * FROM users WHERE id = '$uid'");
$user = $query->fetch_assoc();

$total = 0;
foreach($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['qty'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Sambal Bu Rudy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { burudy: { red: "#B91C1C", gold: "#F59E0B", dark: "#1F2937", light: "#FEF2F2" } } } } };
    </script>
    <script type="text/javascript"
      src="https://app.sandbox.midtrans.com/snap/snap.js"
      data-client-key="SB-Mid-client-XXXXXXXXXXXXXXXX"> </script>
</head>
<body class="bg-gray-50 font-sans text-burudy-dark">

    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-3xl mx-auto px-4 h-20 flex justify-between items-center">
            <a href="cart.php" class="flex items-center gap-2 text-gray-500 hover:text-burudy-red font-medium transition">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <span class="font-serif text-xl font-bold">Pembayaran</span>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto px-4 py-10">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
            <h2 class="text-2xl font-bold mb-6">Informasi Pengiriman</h2>
            
            <form id="payment-form" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Penerima</label>
                    <input type="text" value="<?= $user['name'] ?>" readonly class="w-full bg-gray-100 border border-gray-300 rounded-lg p-3 text-gray-500 cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Alamat Lengkap</label>
                    <textarea id="address" rows="3" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-burudy-red focus:border-transparent" placeholder="Jalan, Nomor Rumah, Kecamatan, Kota..."><?= $user['address'] ?></textarea>
                </div>
                
                <div class="border-t pt-6 mt-6">
                    <div class="flex justify-between items-center mb-6">
                        <span class="text-lg font-medium text-gray-600">Total Tagihan</span>
                        <span class="text-3xl font-bold text-burudy-red">Rp <?= number_format($total, 0, ',', '.') ?></span>
                    </div>
                    
                    <button type="button" id="pay-button" class="w-full bg-burudy-red text-white py-4 rounded-xl font-bold text-lg hover:bg-red-800 transition shadow-lg hover:shadow-xl">
                        Bayar Sekarang (QRIS/VA)
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        const payButton = document.getElementById('pay-button');
        
        payButton.addEventListener('click', async function() {
            const address = document.getElementById('address').value;
            if(!address) return alert("Mohon lengkapi alamat pengiriman!");

            payButton.innerHTML = "Memproses...";
            payButton.disabled = true;

            try {
                // 1. Request Snap Token dari Backend Kita
                const response = await fetch('../api/payment/get_token.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'address=' + encodeURIComponent(address)
                });
                
                const data = await response.json();
                
                if (data.token) {
                    // 2. Munculkan Popup Midtrans
                    window.snap.pay(data.token, {
                        onSuccess: function(result){
                            window.location.href = "history.php?status=success";
                        },
                        onPending: function(result){
                            window.location.href = "history.php?status=pending";
                        },
                        onError: function(result){
                            alert("Pembayaran gagal!");
                            payButton.disabled = false;
                            payButton.innerHTML = "Bayar Sekarang";
                        }
                    });
                } else {
                    alert('Gagal mendapatkan token pembayaran: ' + (data.error || 'Unknown Error'));
                    payButton.disabled = false;
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan koneksi.');
                payButton.disabled = false;
            }
        });
    </script>
</body>
</html>