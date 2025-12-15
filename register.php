<?php
// /register.php
require 'config/database.php';

if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 1. Validasi Email Unik
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        $error = "Email sudah terdaftar, silakan login.";
    } else {
        // 2. Hash Password & Simpan
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $insert = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
        $insert->bind_param("sss", $name, $email, $hashed_password);
        
        if ($insert->execute()) {
            echo "<script>alert('Pendaftaran Berhasil! Silakan Login.'); window.location='login.php';</script>";
            header("Location: login.php");
            exit;
        } else {
            $error = "Gagal mendaftar: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Sambal Bu Rudy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { burudy: { red: "#B91C1C", gold: "#F59E0B", dark: "#1F2937" } } } } };
    </script>
</head>
<body class="bg-gray-50 flex items-center justify-center h-screen font-sans">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border-t-4 border-burudy-gold">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-burudy-dark">Buat Akun Baru</h1>
            <p class="text-gray-500 text-sm">Bergabunglah dan nikmati kemudahannya.</p>
        </div>

        <?php if(isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap</label>
                <input type="text" name="name" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-300 focus:outline-none focus:border-burudy-gold transition">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-300 focus:outline-none focus:border-burudy-gold transition">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-300 focus:outline-none focus:border-burudy-gold transition">
            </div>
            <button type="submit" name="register" class="w-full bg-burudy-gold text-white font-bold py-3 rounded-xl hover:bg-yellow-600 transition transform hover:scale-105 shadow-lg">
                Daftar Sekarang
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600">
            Sudah punya akun? <a href="login.php" class="text-burudy-red font-bold hover:underline">Masuk disini</a>
        </p>
    </div>
</body>
</html>