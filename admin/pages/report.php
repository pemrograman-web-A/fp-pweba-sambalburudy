<?php
// /admin/pages/report.php
// Frontend untuk menampilkan laporan transaksi
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - Bu Rudy Admin</title>
    
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
                    <a href="admin.php" class="text-gray-300 hover:text-burudy-gold transition"><i class="fas fa-home"></i> Dashboard</a>
                    <a href="/fp/logout.php" class="bg-burudy-red text-white px-3 py-1 rounded-full text-sm font-medium hover:bg-red-800 transition">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-serif font-bold text-burudy-red mb-8">Laporan Penjualan</h1>

            <div class="flex justify-between items-center mb-6 bg-white p-4 rounded-xl shadow">
                <div>
                    <span class="text-lg font-medium text-burudy-dark mr-3">Periode:</span>
                    <select id="periodFilter" class="p-2 border border-gray-300 rounded-lg">
                        <option value="7_days">7 Hari Terakhir</option>
                        <option value="this_month">Bulan Ini</option>
                        <option value="last_month">Bulan Lalu</option>
                        <option value="this_year">Tahun Ini</option>
                    </select>
                </div>
                <div id="periodDisplay" class="font-semibold text-burudy-gold">
                    Memuat...
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-burudy-green">
                    <div class="flex items-center">
                        <i class="fas fa-wallet text-3xl text-burudy-green mr-4"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Pendapatan</p>
                            <h2 id="totalRevenue" class="text-2xl font-bold text-burudy-dark">Rp 0</h2>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-burudy-gold">
                    <div class="flex items-center">
                        <i class="fas fa-chart-line text-3xl text-burudy-gold mr-4"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Transaksi</p>
                            <h2 id="totalTransactions" class="text-2xl font-bold text-burudy-dark">0</h2>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-burudy-red">
                    <div class="flex items-center">
                        <i class="fas fa-cubes text-3xl text-burudy-red mr-4"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Item Terjual</p>
                            <h2 id="totalItems" class="text-2xl font-bold text-burudy-dark">0</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg">
                <h2 class="text-xl font-semibold mb-4">Detail Transaksi</h2>
                
                <div id="loading" class="text-center py-8 hidden">
                    <i class="fas fa-spinner fa-spin text-burudy-gold text-2xl"></i> Memuat data...
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Transaksi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Item</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah (Rp)</th>
                            </tr>
                        </thead>
                        <tbody id="transactionTableBody" class="bg-white divide-y divide-gray-200">
                            <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Pilih periode untuk memuat data.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <footer class="bg-burudy-dark py-4 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-gray-400 text-sm">&copy; 2025 Bu Rudy Admin Panel.</p>
        </div>
    </footer>

    <script>
        const API_URL = '../api/report_api.php'; 
        
        const totalRevenueEl = document.getElementById('totalRevenue');
        const totalTransactionsEl = document.getElementById('totalTransactions');
        const totalItemsEl = document.getElementById('totalItems');
        const periodDisplayEl = document.getElementById('periodDisplay');
        const transactionTableBody = document.getElementById('transactionTableBody');
        const loadingEl = document.getElementById('loading');
        const periodFilter = document.getElementById('periodFilter');

        const formatRupiah = (angka) => {
            return 'Rp ' + (new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(angka));
        };

        const loadReportData = async (period) => {
            loadingEl.classList.remove('hidden');
            transactionTableBody.innerHTML = '<tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Memuat...</td></tr>';

            try {
                const response = await fetch(`${API_URL}?period=${period}`);
                
                if (!response.ok) {
                    throw new Error(`Gagal memuat data laporan: Status ${response.status}`);
                }
                
                const data = await response.json();
                
                // 1. Update Metrik Ringkasan
                totalRevenueEl.textContent = formatRupiah(data.totalRevenue);
                totalTransactionsEl.textContent = data.totalTransactions;
                totalItemsEl.textContent = data.totalItems;
                periodDisplayEl.textContent = `(${data.period_info.period} | ${data.period_info.start} s/d ${data.period_info.end})`;

                // 2. Isi Tabel Transaksi
                transactionTableBody.innerHTML = '';
                if (data.transactions.length === 0) {
                    transactionTableBody.innerHTML = '<tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada transaksi dalam periode ini.</td></tr>';
                } else {
                    data.transactions.forEach(tx => {
                        const dateOnly = tx.date.split(' ')[0]; // Ambil hanya tanggal jika formatnya DATETIME
                        
                        const row = `
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${tx.id}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${dateOnly}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-700">${tx.items}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-right text-burudy-green">${formatRupiah(tx.amount)}</td>
                            </tr>
                        `;
                        transactionTableBody.innerHTML += row;
                    });
                }
                
            } catch (error) {
                console.error("Error fetching report:", error);
                totalRevenueEl.textContent = 'Error';
                totalTransactionsEl.textContent = 'Error';
                totalItemsEl.textContent = 'Error';
                periodDisplayEl.textContent = 'Gagal memuat data';
                transactionTableBody.innerHTML = '<tr><td colspan="4" class="px-6 py-4 text-center text-burudy-red">Kesalahan API. Cek Console Log.</td></tr>';
            } finally {
                loadingEl.classList.add('hidden');
            }
        };

        // Event Listener untuk Filter
        periodFilter.addEventListener('change', (e) => {
            loadReportData(e.target.value);
        });

        // Muat data default saat halaman dimuat
        document.addEventListener('DOMContentLoaded', () => {
            // Muat default periode (7_days)
            loadReportData(periodFilter.value);
        });
    </script>

</body>
</html>