<?php
session_start();

// Redirect jika sudah login
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/pages/dashboard.php");
        exit;
    } else if ($_SESSION['role'] == 'user') {
        header("Location: user/pages/home.php"); // Halaman User yang akan kita buat nanti
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sambal Bu Rudy - Pusat Oleh-Oleh Surabaya</title>
    <meta
      name="description"
      content="Pusat oleh-oleh khas Surabaya terlengkap. Sambal Bu Rudy, Spikoe Resep Kuno, Almond Crispy, dan Otaji. Pesan online via WhatsApp." />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:wght@600;700;800&display=swap"
      rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              burudy: {
                red: "#B91C1C",
                gold: "#F59E0B",
                dark: "#1F2937",
                light: "#FEF2F2",
              },
            },
            fontFamily: {
              sans: ["Inter", "sans-serif"],
              serif: ["Playfair Display", "serif"],
            },
          },
        },
      };
    </script>
    
    <link rel="stylesheet" href="assets/css/style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  </head>
  <body class="font-sans text-burudy-dark bg-burudy-light antialiased">
    <nav
      id="navbar"
      class="fixed w-full z-50 transition-all duration-300 bg-white/95 backdrop-blur-sm border-b border-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
          <div class="flex-shrink-0 flex items-center gap-2">
            <span
              class="font-serif text-2xl font-bold text-burudy-red tracking-wide"
              >Bu Rudy</span
            >
          </div>

          <div class="hidden md:flex space-x-8 items-center">
            <a href="#home" class="font-medium hover:text-burudy-red transition"
              >Beranda</a
            >
            <a href="#menu" class="font-medium hover:text-burudy-red transition"
              >Menu Favorit</a
            >
            <a
              href="#partners"
              class="font-medium hover:text-burudy-red transition"
              >Oleh-Oleh Lain</a
            >
            <a
              href="#lokasi"
              class="font-medium hover:text-burudy-red transition"
              >Lokasi</a
            >
            <a
              href="login.php"
              class="text-burudy-dark font-semibold hover:text-burudy-red transition border border-gray-300 px-4 py-2 rounded-full">
              Masuk / Daftar
            </a>
            <a
              href="#menu"
              class="bg-burudy-red text-white px-5 py-2 rounded-full font-semibold hover:bg-red-800 transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
              Pesan Sekarang
            </a>
          </div>

          <div class="md:hidden flex items-center">
            <button
              id="mobile-menu-btn"
              class="text-burudy-dark p-2 rounded-md hover:bg-red-50 hover:text-burudy-red focus:outline-none transition">
              <i class="fas fa-bars text-2xl"></i>
            </button>
          </div>
        </div>
      </div>

      <div
        id="mobile-menu"
        class="hidden absolute top-full left-0 w-full bg-white border-t border-gray-100 shadow-xl md:hidden flex flex-col transition-all duration-300 origin-top z-40">
        <div class="px-4 py-4 space-y-3">
          <a
            href="#home"
            class="block px-4 py-3 rounded-lg text-base font-medium text-gray-700 hover:bg-red-50 hover:text-burudy-red transition"
            >Beranda</a
          >
          <a
            href="#menu"
            class="block px-4 py-3 rounded-lg text-base font-medium text-gray-700 hover:bg-red-50 hover:text-burudy-red transition"
            >Menu Favorit</a
          >
          <a
            href="#partners"
            class="block px-4 py-3 rounded-lg text-base font-medium text-gray-700 hover:bg-red-50 hover:text-burudy-red transition"
            >Oleh-Oleh Lain</a
          >
          <a
            href="#lokasi"
            class="block px-4 py-3 rounded-lg text-base font-medium text-gray-700 hover:bg-red-50 hover:text-burudy-red transition"
            >Lokasi</a
          >
          <a href="login.php" class="block px-4 py-3 rounded-lg text-base font-bold text-burudy-red hover:bg-red-50 transition">
            <i class="fas fa-sign-in-alt mr-2"></i> Masuk / Daftar Akun
          </a>
          <a
            href="#menu"
            class="block mt-4 text-center bg-burudy-red text-white px-4 py-3 rounded-full font-bold hover:bg-red-800 transition shadow-md">
            Pesan Sekarang
          </a>
        </div>
      </div>
    </nav>

    <section
      id="home"
      class="pt-24 pb-16 lg:pt-28 lg:pb-24 overflow-hidden relative">
      <div
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col-reverse lg:flex-row items-center gap-12 lg:gap-20">
        <div
          class="w-full lg:w-1/2 text-center lg:text-left z-10 fade-in-section">
          <span
            class="text-burudy-gold font-bold tracking-wider uppercase text-sm mb-2 block"
            >Oleh-Oleh Asli Surabaya</span
          >
          <h1
            class="text-4xl md:text-5xl lg:text-6xl font-serif font-bold leading-tight mb-6">
            Pedasnya <span class="text-burudy-red">Nendang</span>,<br />
            Gurihnya <span class="text-burudy-red">Terngiang</span>.
          </h1>
          <p
            class="text-lg text-gray-600 mb-8 max-w-lg mx-auto lg:mx-0 leading-relaxed">
            Sedia lengkap Sambal Bu Rudy, Spikoe Resep Kuno, Almond Crispy, dan
            aneka oleh-oleh legendaris Surabaya lainnya.
          </p>
          <div
            class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
            <a
              href="#menu"
              class="bg-burudy-red text-white px-8 py-4 rounded-full font-bold shadow-lg hover:bg-red-800 transition transform hover:scale-105 flex items-center justify-center gap-2">
              <i class="fas fa-shopping-bag"></i> Pesan Oleh-Oleh
            </a>
            <a
                href="https://wa.me/621234567890?text=Halo%20Admin%20Bu%20Rudy!%20%F0%9F%99%8F%0ASaya%20mau%20pesan%20oleh-olehnya%20dong.%0A%0ANama%20Lengkap%3A%20%0AProduk%20yang%20dipesan%3A%20%0AJumlah%20Pesanan%3A%20%0AAlamat%20Pengiriman%3A%20%0A%0AMohon%20dibantu%20total%20harganya%20ya%2C%20terima%20kasih!%20%5E%5E"
                target="_blank"
                class="border-2 border-burudy-red text-burudy-red px-8 py-4 rounded-full font-bold hover:bg-red-50 transition flex items-center justify-center gap-2">
                <i class="fab fa-whatsapp text-xl"></i> Chat Admin
            </a>
          </div>
        </div>
        <div class="w-full lg:w-1/2 relative z-10">
          <div
            class="relative rounded-3xl overflow-hidden shadow-2xl bg-white p-2 fade-in-section">
            <img
              src="assets/images/hero-bg.jpg"
              alt="Paket Sambal Bu Rudy Lengkap"
              class="w-full h-auto object-cover rounded-2xl transform transition hover:scale-105 duration-700"
              loading="eager" />
          </div>
          <div
            class="absolute -top-10 -right-10 w-64 h-64 bg-burudy-gold/20 rounded-full blur-3xl -z-10"></div>
          <div
            class="absolute -bottom-10 -left-10 w-64 h-64 bg-burudy-red/10 rounded-full blur-3xl -z-10"></div>
        </div>
      </div>
    </section>

    <script src="assets/js/script.js"></script>
  </body>
</html>