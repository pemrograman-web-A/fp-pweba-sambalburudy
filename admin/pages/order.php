<?php
// /admin/pages/order.php
// Halaman untuk mengelola pesanan yang masih pending/menunggu.
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Menunggu - Admin Bu Rudy</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
</head>
<body class="font-sans text-burudy-dark bg-gray-100 antialiased">

    <div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-xl p-6">
            <h3 class="text-2xl font-serif font-bold mb-4 text-burudy-dark">Detail Pesanan <span id="detailOrderId"></span></h3>
            <div id="detailContent" class="space-y-3 text-gray-700">
                </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" id="btnCloseDetailModal" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition">Tutup</button>
            </div>
        </div>
    </div>

    <div id="actionConfirmModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-sm p-6">
            <h3 id="confirmTitle" class="text-xl font-serif font-bold mb-4 text-burudy-dark">Konfirmasi Aksi</h3>
            <p id="confirmMessage" class="text-gray-700 mb-6">Apakah Anda yakin ingin melanjutkan?</p>
            
            <div class="flex justify-end space-x-3">
                <button type="button" id="btnCancelAction" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition">Batal</button>
                <button type="button" id="btnConfirmAction" class="px-4 py-2 bg-burudy-red text-white rounded-lg hover:bg-red-800 transition"><i class="fas fa-check"></i> Konfirmasi</button>
            </div>
        </div>
    </div>


    <nav class="bg-burudy-dark text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex-shrink-0">
                    <span class="font-serif text-xl font-bold text-burudy-gold tracking-wide">Bu Rudy Admin</span>
                    <span class="ml-4 text-gray-400">/ Pesanan Menunggu</span>
                </div>
                <a href="admin.php" class="bg-burudy-red text-white px-3 py-1 rounded-full text-sm font-medium hover:bg-red-800 transition">Dashboard</a>
            </div>
        </div>
    </nav>

    <main class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-serif font-bold text-burudy-dark mb-8">Daftar Pesanan Menunggu (Pending)</h1>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-md border-t-4 border-burudy-dark/50">
                    <p class="text-sm font-medium text-gray-500">Total Pesanan Pending</p>
                    <p id="summaryTotalOrders" class="text-3xl font-bold text-burudy-dark mt-1">0</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md border-t-4 border-burudy-gold">
                    <p class="text-sm font-medium text-gray-500">Total Nilai Pesanan</p>
                    <p id="summaryTotalValue" class="text-3xl font-bold text-burudy-gold mt-1">Rp 0</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md border-t-4 border-burudy-red">
                    <p class="text-sm font-medium text-gray-500">Perlu Konfirmasi Stok</p>
                    <p id="summaryStockConfirm" class="text-3xl font-bold text-burudy-red mt-1">0</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Pesanan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan & Kontak</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detail Item</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Harga</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="orderTableBody" class="bg-white divide-y divide-gray-200">
                        <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Memuat data pesanan...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <footer class="bg-burudy-dark py-4 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-gray-400 text-sm">&copy; 2025 Bu Rudy Admin Panel.</p>
        </div>
    </footer>

    <script>
        // PERBAIKAN PATH API: Mengarah ke /admin/api/order_api.php
        const API_URL = '../api/order_api.php';
        const orderTableBody = document.getElementById('orderTableBody');

        // Detail Modal DOM
        const detailModal = document.getElementById('detailModal');
        const detailOrderId = document.getElementById('detailOrderId');
        const detailContent = document.getElementById('detailContent');
        const btnCloseDetailModal = document.getElementById('btnCloseDetailModal');

        // Aksi Konfirmasi Modal DOM
        const actionConfirmModal = document.getElementById('actionConfirmModal');
        const confirmTitle = document.getElementById('confirmTitle');
        const confirmMessage = document.getElementById('confirmMessage');
        const btnCancelAction = document.getElementById('btnCancelAction');
        const btnConfirmAction = document.getElementById('btnConfirmAction');

        // Summary Card DOM
        const summaryTotalOrders = document.getElementById('summaryTotalOrders');
        const summaryTotalValue = document.getElementById('summaryTotalValue');
        const summaryStockConfirm = document.getElementById('summaryStockConfirm');


        // Variabel untuk menampung aksi yang akan dieksekusi
        let pendingOrderId = null;
        let pendingNewStatus = null;

        // Helper: Format Rupiah
        const formatRupiah = (angka) => {
            return 'Rp ' + (new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(angka));
        };

        // Helper: Menentukan class badge berdasarkan status
        const getStatusBadge = (status) => {
            let className = '';
            let displayText = status;

            if (status === 'Menunggu Pembayaran') {
                className = 'bg-yellow-100 text-yellow-800';
            } else if (status === 'Perlu Konfirmasi Stok') {
                className = 'bg-red-100 text-burudy-red';
            } else if (status === 'Diproses') {
                className = 'bg-blue-100 text-blue-800';
            } else if (status === 'Dibatalkan') {
                className = 'bg-gray-300 text-gray-700';
            } else if (status === 'Selesai') {
                className = 'bg-burudy-green/20 text-burudy-green';
            } else {
                className = 'bg-gray-100 text-gray-800';
            }
            return `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${className}">${displayText}</span>`;
        };
        
        // 1. READ: Memuat Data Pesanan
        const loadOrders = async () => {
            orderTableBody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500"><i class="fas fa-spinner fa-spin"></i> Memuat data pesanan...</td></tr>';
            
            let totalOrders = 0;
            let totalValue = 0;
            let stockConfirmCount = 0;

            try {
                const response = await fetch(API_URL);
                if (!response.ok) throw new Error("Gagal mengambil data pesanan.");
                const orders = await response.json();
                
                orderTableBody.innerHTML = '';
                
                if (orders.length === 0) {
                    orderTableBody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada pesanan menunggu.</td></tr>';
                }

                orders.forEach(order => {
                    totalOrders++;
                    totalValue += order.total_amount;
                    if (order.status === 'Perlu Konfirmasi Stok') {
                        stockConfirmCount++;
                    }

                    const row = orderTableBody.insertRow();
                    row.className = 'hover:bg-gray-50';

                    // Tampilkan kontak/alamat yang relevan
                    const customerDetail = order.customer_phone || (order.shipping_address ? order.shipping_address.substring(0, 20) + '...' : '-');
                    const customerDisplay = `${order.customer_name} <span class="text-gray-500">(${customerDetail})</span>`;

                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${order.order_id}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${customerDisplay}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 truncate max-w-xs">${order.item_details}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-burudy-red">${formatRupiah(order.total_amount)}</td>
                        <td class="px-6 py-4 whitespace-nowrap">${getStatusBadge(order.status)}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <button onclick="showOrderDetail('${order.order_id}', '${order.customer_name}', '${order.shipping_address}', '${order.item_details}', ${order.total_amount})" class="text-blue-600 hover:text-blue-900 mr-3"><i class="fas fa-eye"></i> Detail</button>
                            <button onclick="showConfirmModal('${order.order_id}', 'Diproses')" class="text-burudy-green hover:text-green-700 mr-3"><i class="fas fa-check"></i> Proses</button>
                            <button onclick="showConfirmModal('${order.order_id}', 'Dibatalkan')" class="text-gray-500 hover:text-burudy-dark"><i class="fas fa-times"></i> Batalkan</button>
                        </td>
                    `;
                });
                
            } catch (error) {
                console.error("Error loading orders:", error);
                orderTableBody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-burudy-red">Gagal memuat data pesanan. Cek koneksi API.</td></tr>';
            } finally {
                // Update Summary Cards
                summaryTotalOrders.textContent = totalOrders;
                summaryTotalValue.textContent = formatRupiah(totalValue);
                summaryStockConfirm.textContent = stockConfirmCount;
            }
        };

        // 2. UPDATE: Mengubah Status Pesanan (Fungsi yang dieksekusi)
        const executeChangeStatus = async () => {
            const orderId = pendingOrderId;
            const newStatus = pendingNewStatus;

            actionConfirmModal.classList.remove('flex');
            actionConfirmModal.classList.add('hidden');

            if (!orderId || !newStatus) return;

            try {
                const response = await fetch(`${API_URL}?id=${orderId}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ status: newStatus })
                });

                const result = await response.json();

                if (response.ok) {
                    alert(result.message);
                    loadOrders(); 
                } else {
                    alert('Operasi Gagal: ' + result.message);
                }

            } catch (error) {
                console.error('Error changing status:', error);
                alert('Terjadi kesalahan koneksi saat mengubah status.');
            } finally {
                pendingOrderId = null;
                pendingNewStatus = null;
            }
        };

        // 3a. Menampilkan Modal Konfirmasi Aksi
        const showConfirmModal = (orderId, newStatus) => {
            pendingOrderId = orderId;
            pendingNewStatus = newStatus;

            const actionVerb = newStatus === 'Diproses' ? 'memproses' : 'membatalkan';
            const title = newStatus === 'Diproses' ? 'Konfirmasi Proses' : 'Konfirmasi Pembatalan';
            const message = `Apakah Anda yakin ingin **${actionVerb}** pesanan <strong>${orderId}</strong>? Status akan diubah menjadi **${newStatus}**.`;

            confirmTitle.textContent = title;
            confirmMessage.innerHTML = message;

            // Atur warna tombol konfirmasi
            if (newStatus === 'Dibatalkan') {
                btnConfirmAction.classList.remove('bg-burudy-green', 'hover:bg-green-700');
                btnConfirmAction.classList.add('bg-burudy-red', 'hover:bg-red-800');
            } else {
                btnConfirmAction.classList.remove('bg-burudy-red', 'hover:bg-red-800');
                btnConfirmAction.classList.add('bg-burudy-green', 'hover:bg-green-700');
            }

            actionConfirmModal.classList.remove('hidden');
            actionConfirmModal.classList.add('flex');
        };

        // 3b. Menampilkan Modal Detail Pesanan
        const showOrderDetail = (orderId, customerName, address, items, total) => {
            detailOrderId.textContent = orderId;
            detailContent.innerHTML = `
                <p><strong>Nama Pelanggan:</strong> ${customerName}</p>
                <p><strong>Alamat Pengiriman:</strong> ${address}</p>
                <p><strong>Daftar Item:</strong> ${items}</p>
                <p class="mt-4 text-lg"><strong>Total Pembayaran:</strong> <span class="text-burudy-red font-bold">${formatRupiah(total)}</span></p>
            `;
            detailModal.classList.remove('hidden');
            detailModal.classList.add('flex');
        };

        // Event Listeners
        btnCloseDetailModal.onclick = () => {
            detailModal.classList.remove('flex');
            detailModal.classList.add('hidden');
        };

        btnConfirmAction.onclick = executeChangeStatus;
        btnCancelAction.onclick = () => {
            actionConfirmModal.classList.remove('flex');
            actionConfirmModal.classList.add('hidden');
            pendingOrderId = null;
            pendingNewStatus = null;
        };

        // Jalankan saat halaman dimuat
        document.addEventListener('DOMContentLoaded', loadOrders);
    </script>
</body>
</html>