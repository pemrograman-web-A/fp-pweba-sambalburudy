<?php
// /user/pages/checkout.php
session_start();
require '../../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user' || empty($_SESSION['cart'])) {
    header("Location: home.php");
    exit;
}

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
    <title>Checkout Manual - Sambal Bu Rudy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { burudy: { red: "#B91C1C", gold: "#F59E0B", dark: "#1F2937", light: "#FEF2F2" } } } } };
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-50 font-sans text-burudy-dark">

    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-3xl mx-auto px-4 h-20 flex justify-between items-center">
            <a href="cart.php" class="flex items-center gap-2 text-gray-500 hover:text-burudy-red font-medium transition">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <span class="font-serif text-xl font-bold">Pembayaran Manual</span>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto px-4 py-10">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
            
            <div class="mb-8 border-b border-gray-100 pb-8">
                <h2 class="text-xl font-bold mb-4">1. Alamat Pengiriman</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nama Penerima</label>
                        <input type="text" value="<?= htmlspecialchars($user['name']) ?>" readonly class="w-full bg-gray-100 border border-gray-300 rounded-lg p-3 text-gray-500 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Alamat Lengkap</label>
                        <textarea id="address" rows="2" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-burudy-red focus:border-transparent" placeholder="Jalan, Nomor Rumah, Kecamatan, Kota..."><?= htmlspecialchars($user['address']) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="mb-8 text-center">
                <h2 class="text-xl font-bold mb-4">2. Scan Untuk Membayar</h2>
                <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 inline-block">
                    <img src="../../images/qris.png" alt="Scan QRIS Bu Rudy" class="w-48 h-48 object-contain mx-auto mb-3 border border-gray-300 rounded-lg" onerror="this.src='https://via.placeholder.com/200?text=QR+CODE+PLACEHOLDER'">
                    
                    <p class="font-bold text-gray-700">Total Tagihan:</p>
                    <p class="text-3xl font-bold text-burudy-red mb-2">Rp <?= number_format($total, 0, ',', '.') ?></p>
                    <p class="text-xs text-gray-500">Scan menggunakan GoPay, OVO, Dana, atau Mobile Banking.</p>
                </div>
            </div>
            
            <div class="space-y-3">
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded-lg text-sm mb-4">
                    <i class="fas fa-info-circle mr-2"></i> 
                    <strong>Penting:</strong> Setelah klik tombol di bawah, Anda akan diarahkan ke Google Form untuk upload bukti pembayaran. Jangan lupa catat <strong>Order ID</strong> Anda nanti.
                </div>

                <button type="button" id="confirm-button" class="w-full bg-burudy-red text-white py-4 rounded-xl font-bold text-lg hover:bg-red-800 transition shadow-lg hover:shadow-xl flex justify-center items-center gap-2">
                    <span>Konfirmasi & Upload Bukti</span>
                    <i class="fas fa-upload"></i>
                </button>
            </div>

        </div>
    </main>

    <script>
        // --- KONFIGURASI LINK GOOGLE FORM ---
        // Masukkan Link Google Form Anda di sini
        const GOOGLE_FORM_URL = "https://docs.google.com/forms/d/e/1FAIpQLSffnkhc-6GMqGAuX9F2PDUTTW4eloCUJTM33j8Ap0jvxbRE3w/viewform?usp=header";
        const btn = document.getElementById('confirm-button');
        
        btn.addEventListener('click', async function() {
            const address = document.getElementById('address').value;
            if(!address) return alert("Mohon lengkapi alamat pengiriman!");

            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            btn.disabled = true;

            try {
                // 1. Simpan Pesanan ke Database via API
                const response = await fetch('../api/order/place_order.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'address=' + encodeURIComponent(address)
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    // Simpan URL GForm ke Storage agar bisa dibuka lagi di halaman success jika perlu
                    localStorage.setItem('gform_url', GOOGLE_FORM_URL);

                    // 2. Buka Google Form di Tab Baru
                    window.open(GOOGLE_FORM_URL, '_blank');

                    // 3. Redirect Tab Ini ke Halaman Sukses
                    // Kita beri jeda sedikit agar user sadar ada tab baru terbuka
                    setTimeout(() => {
                        window.location.href = "success.php?order_id=" + data.order_id;
                    }, 500);

                } else {
                    throw new Error(data.message);
                }
            } catch (err) {
                console.error(err);
                alert('Gagal memproses pesanan: ' + err.message);
                btn.innerHTML = 'Konfirmasi & Upload Bukti';
                btn.disabled = false;
            }
        });
    </script>
</body>
</html>