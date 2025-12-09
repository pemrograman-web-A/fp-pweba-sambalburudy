<?php
// /user/pages/profile.php
session_start();
require '../../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: ../../login.php");
    exit;
}

$uid = $_SESSION['user_id'];
$msg = "";

// Handle Update Profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("UPDATE users SET name = ?, phone = ?, address = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $phone, $address, $uid);
    
    if ($stmt->execute()) {
        $_SESSION['name'] = $name; // Update session name
        $msg = "<div class='bg-green-100 text-green-700 p-3 rounded mb-4'>✅ Profil berhasil diperbarui!</div>";
    } else {
        $msg = "<div class='bg-red-100 text-red-700 p-3 rounded mb-4'>❌ Gagal memperbarui profil.</div>";
    }
}

// Ambil Data Terbaru
$query = $conn->query("SELECT * FROM users WHERE id = '$uid'");
$user = $query->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Bu Rudy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { burudy: { red: "#B91C1C", gold: "#F59E0B", dark: "#1F2937", light: "#FEF2F2" } } } } };
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="font-sans text-gray-800 bg-gray-50 antialiased">
    <nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 h-16 flex justify-between items-center">
            <a href="home.php" class="flex items-center gap-2 text-gray-500 hover:text-red-700 font-medium"><i class="fas fa-arrow-left"></i> Kembali Belanja</a>
            <span class="font-serif text-xl font-bold">Manajemen Akun</span>
        </div>
    </nav>
    <main class="py-10 px-4">
        <div class="max-w-4xl mx-auto">
            
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 flex flex-col md:flex-row items-center gap-6 border-l-4 border-burudy-red">
                <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center text-3xl text-burudy-red"><i class="fas fa-user"></i></div>
                <div class="text-center md:text-left flex-1">
                    <h1 class="text-2xl font-bold"><?= htmlspecialchars($user['name']) ?></h1>
                    <p class="text-gray-500"><?= htmlspecialchars($user['email']) ?></p>
                </div>
                <div class="flex gap-2">
                    <a href="history.php" class="text-white bg-burudy-gold font-bold px-4 py-2 rounded-lg hover:bg-yellow-600 transition">Riwayat Pesanan</a>
                    <a href="../../logout.php" class="text-red-600 font-bold border border-red-200 px-4 py-2 rounded-lg hover:bg-red-50 transition">Logout</a>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h2 class="text-xl font-bold mb-6 border-b pb-4">Edit Data Diri</h2>
                <?= $msg ?>
                
                <form method="POST" class="space-y-5">
                    <div class="grid md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-bold mb-1">Nama Lengkap</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required class="w-full border p-3 rounded-lg bg-gray-50 focus:ring-2 focus:ring-burudy-gold">
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1">No HP</label>
                            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" class="w-full border p-3 rounded-lg bg-gray-50 focus:ring-2 focus:ring-burudy-gold">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1">Email</label>
                        <input type="email" value="<?= htmlspecialchars($user['email']) ?>" class="w-full border p-3 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed" readonly>
                        <p class="text-xs text-gray-400 mt-1">*Email tidak dapat diubah</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1">Alamat Pengiriman</label>
                        <textarea name="address" rows="3" class="w-full border p-3 rounded-lg bg-gray-50 focus:ring-2 focus:ring-burudy-gold"><?= htmlspecialchars($user['address']) ?></textarea>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="bg-burudy-red text-white px-6 py-3 rounded-xl font-bold hover:bg-red-800 shadow-lg transition transform hover:-translate-y-1">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>