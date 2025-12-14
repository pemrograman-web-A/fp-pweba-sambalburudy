<?php
// /admin/pages/manajemen.php
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk - Bu Rudy Admin</title>
    
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
            <h1 class="text-3xl font-serif font-bold text-burudy-red mb-8">Manajemen Produk</h1>

            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold">Daftar Produk</h2>
                    <button id="btnTambahProduk" class="bg-burudy-green text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-plus"></i> Tambah Produk Baru
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tabelProdukBody" class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">Memuat data produk...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div id="loading" class="text-center py-4 hidden">
                    <i class="fas fa-spinner fa-spin text-burudy-gold text-2xl"></i> Memuat...
                </div>
            </div>
        </div>
    </main>

    <div id="productModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6">
            <h3 id="modalTitle" class="text-2xl font-serif font-bold mb-4 text-burudy-dark">Tambah Produk</h3>
            <form id="productForm">
                <input type="hidden" id="productId">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Produk</label>
                    <input type="text" id="name" name="name" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                </div>
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea id="description" name="description" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700">Harga (Rp)</label>
                        <input type="number" id="price" name="price" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                    </div>
                    <div>
                        <label for="stock" class="block text-sm font-medium text-gray-700">Stok</label>
                        <input type="number" id="stock" name="stock" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                    </div>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" id="closeModal" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition">Batal</button>
                    <button type="submit" id="submitButton" class="px-4 py-2 bg-burudy-red text-white rounded-lg hover:bg-red-800 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-sm p-6">
            <h3 class="text-xl font-serif font-bold mb-4 text-burudy-red">Konfirmasi Penghapusan</h3>
            <p id="deleteMessage" class="text-gray-700 mb-6">Apakah Anda yakin ingin menghapus produk **[Nama Produk]**?</p>
            
            <div class="flex justify-end space-x-3">
                <button type="button" id="btnCancelDelete" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition">Batal</button>
                <button type="button" id="btnConfirmDelete" class="px-4 py-2 bg-burudy-red text-white rounded-lg hover:bg-red-800 transition"><i class="fas fa-trash-alt"></i> Hapus Permanen</button>
            </div>
        </div>
    </div>

    <script>
        // PERBAIKAN PATH API: Mengarah ke /admin/api/product/
        const API_URL = '../api/product_api.php'; 
        const tabelProdukBody = document.getElementById('tabelProdukBody');
        const productModal = document.getElementById('productModal');
        const productForm = document.getElementById('productForm');
        const modalTitle = document.getElementById('modalTitle');
        const submitButton = document.getElementById('submitButton');

        // Modal Hapus Kustom
        const deleteModal = document.getElementById('deleteModal');
        const deleteMessage = document.getElementById('deleteMessage');
        const btnCancelDelete = document.getElementById('btnCancelDelete');
        const btnConfirmDelete = document.getElementById('btnConfirmDelete');
        let productIdToDelete = null;

        const formatRupiah = (angka) => {
            return 'Rp ' + (new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(angka));
        };

        const loadProducts = async () => {
            tabelProdukBody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Memuat data produk...</td></tr>';
            try {
                const response = await fetch(API_URL);
                if (!response.ok) throw new Error("Gagal mengambil data produk.");
                const products = await response.json();
                
                tabelProdukBody.innerHTML = ''; 
                
                if (products.length === 0) {
                    tabelProdukBody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada data produk.</td></tr>';
                    return;
                }

                products.forEach(product => {
                    const displayDescription = (product.description && product.description !== '0') ? product.description : '-';
                    
                    // RAW DATA: Escaping quotes untuk mencegah error JS pada onclick
                    const rawName = (product.name || '').replace(/'/g, "\\'"); 
                    const rawDescription = (product.description || '').replace(/'/g, "\\'");
                    
                    row = tabelProdukBody.insertRow();
                    row.className = 'hover:bg-gray-50';
                    
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${product.id}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${product.name}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 max-w-xs overflow-hidden truncate">${displayDescription}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-burudy-gold">${formatRupiah(product.price)}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium ${product.stock < 20 ? 'text-orange-600' : 'text-burudy-green'}">${product.stock}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <button onclick="openEditModal(
                                '${product.id}', 
                                '${rawName}', 
                                '${rawDescription}', 
                                ${product.price}, 
                                ${product.stock})" 
                                class="text-blue-600 hover:text-blue-900 mx-2"><i class="fas fa-edit"></i> Edit</button>
                            <button onclick="showDeleteConfirmation('${product.id}', '${rawName}')" class="text-burudy-red hover:text-red-800 mx-2"><i class="fas fa-trash-alt"></i> Hapus</button>
                        </td>
                    `;
                });
            } catch (error) {
                console.error("Error loading products:", error);
                tabelProdukBody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-burudy-red">Gagal memuat data. Cek koneksi API atau Console Log.</td></tr>';
            }
        };

        const openEditModal = (id, name, description, price, stock) => {
            modalTitle.textContent = 'Edit Produk: ' + id;
            submitButton.textContent = 'Simpan Perubahan';
            document.getElementById('productId').value = id; 
            document.getElementById('name').value = name;
            document.getElementById('description').value = description; 
            document.getElementById('price').value = price;
            document.getElementById('stock').value = stock;
            productModal.classList.remove('hidden');
            productModal.classList.add('flex');
        };

        document.getElementById('closeModal').onclick = () => {
            productModal.classList.remove('flex');
            productModal.classList.add('hidden');
            productForm.reset();
        };

        // 3. CREATE/UPDATE: Submit Form
        productForm.onsubmit = async (e) => {
            e.preventDefault();
            const id = document.getElementById('productId').value;
            const isUpdate = id !== '';
            
            const method = isUpdate ? 'PUT' : 'POST';
            let url = API_URL;
            if (isUpdate) url += `?id=${id}`;

            const productData = {
                name: document.getElementById('name').value,
                description: document.getElementById('description').value,
                price: document.getElementById('price').value,
                stock: document.getElementById('stock').value,
            };

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(productData)
                });
                
                const result = await response.json(); 
                
                if (response.ok) {
                    alert(result.message);
                    productModal.classList.remove('flex');
                    productModal.classList.add('hidden');
                    loadProducts(); 
                } else {
                    alert('Operasi Gagal: ' + result.message); 
                }
            } catch (error) {
                console.error('Error submitting form:', error);
                alert('Terjadi kesalahan koneksi atau respons API tidak valid.');
            }
        };

        // --- FUNGSI HAPUS CUSTOM MODAL ---

        // 4a. Menampilkan modal konfirmasi
        const showDeleteConfirmation = (id, name) => {
            productIdToDelete = id;
            
            // Mengatur pesan konfirmasi kustom
            deleteMessage.innerHTML = `Apakah Anda yakin ingin menghapus produk <strong>${name} (${id})</strong>? Aksi ini tidak dapat dibatalkan.`;
            
            // Menampilkan modal
            deleteModal.classList.remove('hidden');
            deleteModal.classList.add('flex');
        };

        // 4b. Fungsi yang dieksekusi setelah konfirmasi
        const executeDelete = async () => {
            const id = productIdToDelete;
            
            // Menyembunyikan modal
            deleteModal.classList.remove('flex');
            deleteModal.classList.add('hidden');

            if (!id) return;

            try {
                const response = await fetch(`${API_URL}?id=${id}`, {
                    method: 'DELETE'
                });

                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    const result = await response.json();
                    
                    if (response.ok) {
                        alert(result.message);
                        loadProducts(); 
                    } else {
                        alert('Penghapusan Gagal: ' + result.message);
                    }
                } else {
                    console.error("Respons server tidak valid/fatal error:", await response.text());
                    alert('Penghapusan Gagal: Server mengembalikan kesalahan fatal (500). Cek Console Log.');
                }

            } catch (error) {
                console.error('Error saat fetch DELETE:', error);
                alert('Terjadi kesalahan koneksi saat menghapus.');
            } finally {
                productIdToDelete = null; // Reset ID
            }
        };

        // Event Listeners untuk tombol Hapus di modal kustom
        btnConfirmDelete.onclick = executeDelete;
        btnCancelDelete.onclick = () => {
            deleteModal.classList.remove('flex');
            deleteModal.classList.add('hidden');
            productIdToDelete = null;
        };
        
        // Fungsi utama yang dipanggil dari tombol tabel (memanggil modal)
        const deleteProduct = (id, name) => {
            showDeleteConfirmation(id, name);
        };

        document.addEventListener('DOMContentLoaded', loadProducts);

    </script>

    <footer class="bg-burudy-dark py-4 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-gray-400 text-sm">&copy; 2025 Bu Rudy Admin Panel.</p>
        </div>
    </footer>

</body>
</html>