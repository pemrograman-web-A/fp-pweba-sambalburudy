<?php
// /user/pages/home.php
session_start();
require '../../config/database.php';

// Cek Keamanan Session
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: ../../login.php");
    exit;
}

// Hitung item keranjang untuk badge navbar
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - Sambal Bu Rudy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              burudy: { red: "#B91C1C", gold: "#F59E0B", dark: "#1F2937", light: "#FEF2F2" },
            },
            fontFamily: { sans: ["Inter", "sans-serif"], serif: ["Playfair Display", "serif"] },
          },
        },
      };
    </script>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="font-sans text-burudy-dark bg-burudy-light antialiased">

    <nav class="fixed w-full z-50 bg-white/95 backdrop-blur-sm border-b border-gray-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-2">
                    <span class="font-serif text-2xl font-bold text-burudy-red tracking-wide">Bu Rudy</span>
                </div>

                <div class="flex items-center gap-6">
                    <a href="cart.php" class="relative group">
                        <i class="fas fa-shopping-cart text-2xl text-gray-600 group-hover:text-burudy-red transition"></i>
                        <span id="cart-badge" class="absolute -top-2 -right-2 bg-burudy-red text-white text-xs font-bold h-5 w-5 rounded-full flex items-center justify-center <?= $cart_count > 0 ? '' : 'hidden' ?>">
                            <?= $cart_count ?>
                        </span>
                    </a>
                    
                    <div class="flex items-center gap-3">
                        <span class="hidden md:block font-medium text-gray-700">Halo, <?= htmlspecialchars($_SESSION['name']) ?></span>
                        <a href="../../logout.php" class="text-sm bg-gray-100 hover:bg-red-100 text-gray-600 hover:text-red-700 px-4 py-2 rounded-full transition font-semibold">
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <section class="pt-28 pb-16 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col-reverse lg:flex-row items-center gap-12">
            <div class="w-full lg:w-1/2 text-center lg:text-left">
                <h1 class="text-4xl md:text-5xl font-serif font-bold leading-tight mb-6">
                    Selamat Belanja,<br><span class="text-burudy-red"><?= htmlspecialchars($_SESSION['name']) ?>!</span>
                </h1>
                <p class="text-lg text-gray-600 mb-8">Silakan pilih oleh-oleh favoritmu langsung dari sini.</p>
                <a href="#menu" class="bg-burudy-red text-white px-8 py-4 rounded-full font-bold shadow-lg hover:bg-red-800 transition">
                    Mulai Pesan
                </a>
            </div>
            <div class="w-full lg:w-1/2 relative z-10">
                <div class="relative rounded-3xl overflow-hidden shadow-2xl bg-white p-2">
                    <img src="../../assets/images/hero-bg.jpg" alt="Hero" class="w-full h-auto object-cover rounded-2xl">
                </div>
            </div>
        </div>
    </section>

    <section id="menu" class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-serif font-bold mb-4">Katalog Produk</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php
                // Ambil produk dari database
                $query = "SELECT * FROM products ORDER BY id DESC";
                $result = $conn->query($query);

                if ($result->num_rows > 0) {
                    while($product = $result->fetch_assoc()) {
                ?>
                <div class="bg-gray-50 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition border border-gray-100 flex flex-col">
                    <div class="h-64 overflow-hidden relative">
                        <img src="../../assets/images/<?= $product['image'] ?>" alt="<?= $product['name'] ?>" class="w-full h-full object-cover">
                    </div>
                    <div class="p-6 flex-1 flex flex-col">
                        <h3 class="text-xl font-bold mb-2 font-serif"><?= $product['name'] ?></h3>
                        <p class="text-gray-500 text-sm mb-4 flex-1"><?= $product['description'] ?></p>
                        <div class="flex justify-between items-center pt-4 border-t border-gray-200 mt-auto">
                            <span class="text-lg font-bold text-burudy-red">Rp <?= number_format($product['price'], 0, ',', '.') ?></span>
                            <button onclick="addToCart(<?= $product['id'] ?>)" class="bg-burudy-green hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-2">
                                <i class="fas fa-cart-plus"></i> Tambah
                            </button>
                        </div>
                    </div>
                </div>
                <?php 
                    } 
                } else {
                    echo "<p class='text-center col-span-3'>Belum ada produk yang tersedia.</p>";
                }
                ?>
            </div>
        </div>
    </section>

    <script>
        function addToCart(productId) {
            const formData = new FormData();
            formData.append('action', 'add');
            formData.append('product_id', productId);

            fetch('../api/cart/cart_api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    // Update Badge Cart
                    const badge = document.getElementById('cart-badge');
                    badge.innerText = data.cart_count;
                    badge.classList.remove('hidden');
                    alert('✅ Produk berhasil masuk keranjang!');
                } else {
                    alert('❌ Gagal: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>

</body>
</html>