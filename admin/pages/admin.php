<?php
// /admin/pages/admin.php
// PASTIKAN file ini memiliki ekstensi .php
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sambal Bu Rudy</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        burudy: {
                            red: '#B91C1C',
                            gold: '#F59E0B',
                            dark: '#1F2937',
                            light: '#FEF2F2',
                            green: '#059669'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    }
                }
            }
        }

        // Helper untuk format Rupiah (Client-side)
        function formatRupiah(angka) {
            return 'Rp ' + (new Intl.NumberFormat('id-ID').format(angka));
        }

        // --- FUNGSI UTAMA UNTUK MENGAMBIL DATA DARI API ---
        document.addEventListener('DOMContentLoaded', () => {
            // Mengganti API_URL di sini
            const API_URL = '../api/dashboard_api.php'; 
            
            fetch(API_URL) 
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Gagal mengambil data dari API: Status ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    // 1. Mengisi Card Statistik
                    document.getElementById('pesanan_baru').textContent = data.pesanan_baru;
                    document.getElementById('pendapatan_hari_ini').textContent = formatRupiah(data.pendapatan_hari_ini);
                    document.getElementById('produk_stok_rendah').textContent = data.produk_stok_rendah;
                    document.getElementById('review_baru').textContent = data.review_baru;

                    // 2. Mengisi Aktivitas Terbaru
                    const activityList = document.getElementById('activity-list');
                    activityList.innerHTML = ''; 
                    
                    if (data.aktivitas_terbaru && data.aktivitas_terbaru.length > 0) {
                        data.aktivitas_terbaru.forEach(aktivitas => {
                            const li = document.createElement('li');
                            li.className = 'py-3 flex justify-between items-center';
                            li.innerHTML = `
                                <div>
                                    <p class="font-medium ${aktivitas.status_class}">${aktivitas.desc}</p>
                                    <span class="text-sm text-gray-500">${aktivitas.detail}</span>
                                </div>
                                <span class="text-sm ${aktivitas.status_class} font-semibold">${aktivitas.waktu}</span>
                            `;
                            activityList.appendChild(li);
                        });
                    } else {
                         activityList.innerHTML = '<li class="py-3 text-center text-gray-500">Tidak ada aktivitas terbaru.</li>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal memuat data dashboard. Cek koneksi API.');
                    
                    // Menonaktifkan/mereset tampilan card jika API gagal
                    document.getElementById('activity-list').innerHTML = '<li class="py-3 text-center text-burudy-red">Error: Gagal memuat data aktivitas.</li>';
                    document.getElementById('pesanan_baru').textContent = 'X';
                    document.getElementById('pendapatan_hari_ini').textContent = 'X';
                    document.getElementById('produk_stok_rendah').textContent = 'X';
                    document.getElementById('review_baru').textContent = 'X';
                });
        });

    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="font-sans text-burudy-dark bg-gray-100 antialiased">

    <nav class="bg-burudy-dark text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex-shrink-0">
                    <span class="font-serif text-xl font-bold text-burudy-gold tracking-wide">Bu Rudy Admin</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="#" class="text-gray-300 hover:text-burudy-gold transition"><i class="fas fa-bell"></i> Notifikasi</a>
                    <a href="/fp/logout.php" class="bg-burudy-red text-white px-3 py-1 rounded-full text-sm font-medium hover:bg-red-800 transition">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"> 
            <h1 class="text-3xl font-serif font-bold text-burudy-red mb-8">Dashboard Utama</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                
                <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-burudy-red">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase">Pesanan Baru (24J)</p>
                            <p id="pesanan_baru" class="text-3xl font-bold text-burudy-dark mt-1">...</p> 
                        </div>
                        <i class="fas fa-shopping-cart text-burudy-red text-3xl opacity-50"></i>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-burudy-gold">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase">Pendapatan Hari Ini</p>
                            <p id="pendapatan_hari_ini" class="text-xl font-bold text-burudy-dark mt-1">...</p>
                        </div>
                        <i class="fas fa-rupiah-sign text-burudy-gold text-3xl opacity-50"></i>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-orange-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase">Produk Stok Rendah</p>
                            <p id="produk_stok_rendah" class="text-3xl font-bold text-burudy-dark mt-1 text-orange-600">...</p>
                        </div>
                        <i class="fas fa-warehouse text-orange-500 text-3xl opacity-50"></i>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-burudy-green">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase">Review/Ulasan</p>
                            <p id="review_baru" class="text-3xl font-bold text-burudy-dark mt-1">...</p>
                        </div>
                        <i class="fas fa-star text-burudy-green text-3xl opacity-50"></i>
                    </div>
                </div>

            </div>
            
            <h2 class="text-2xl font-serif font-bold text-burudy-dark mb-6">Aksi Cepat</h2>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <a href="order.php" class="block bg-white p-6 rounded-xl shadow-md hover:shadow-xl transition text-center border border-gray-200">
                    <i class="fas fa-hourglass-start text-4xl text-burudy-red mb-3"></i>
                    <p class="font-semibold">Pesanan Menunggu</p>
                </a>
                <a href="manajemen.php" class="block bg-white p-6 rounded-xl shadow-md hover:shadow-xl transition text-center border border-gray-200">
                    <i class="fas fa-box-open text-4xl text-burudy-gold mb-3"></i>
                    <p class="font-semibold">Manajemen Produk</p>
                </a>
                <a href="content.php" class="block bg-white p-6 rounded-xl shadow-md hover:shadow-xl transition text-center border border-gray-200">
                    <i class="fas fa-edit text-4xl text-burudy-dark mb-3"></i>
                    <p class="font-semibold">Edit Konten Halaman</p>
                </a>
                <a href="report.php" class="block bg-white p-6 rounded-xl shadow-md hover:shadow-xl transition text-center border border-gray-200">
                    <i class="fas fa-chart-line text-4xl text-burudy-green mb-3"></i>
                    <p class="font-semibold">Laporan Penjualan</p>
                </a>
            </div>

            <h2 class="text-2xl font-serif font-bold text-burudy-dark mt-12 mb-6">Aktivitas Terbaru</h2>
            
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <ul id="activity-list" class="divide-y divide-gray-200">
                    <li class="py-3 text-center text-gray-500">Memuat aktivitas...</li>
                </ul>
            </div>

        </div>
    </main>

    <footer class="bg-burudy-dark py-4 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-gray-400 text-sm">&copy; 2025 Bu Rudy Admin Panel.</p>
        </div>
    </footer>

</body>
</html>