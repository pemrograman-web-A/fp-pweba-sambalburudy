<?php
session_start();
require 'config/database.php'; 

if (isset($_SESSION['role'])) {
    // Redirect jika user iseng buka halaman login padahal sudah login
    if ($_SESSION['role'] == 'admin') header("Location: admin/pages/admin.php");
    else header("Location: user/pages/home.php");
    exit;
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 1. Cek Hardcoded Admin
    if ($email === 'admin' && $password === 'admin123') {
        $_SESSION['user_id'] = 0;
        $_SESSION['name'] = 'Admin Bu Rudy';
        $_SESSION['role'] = 'admin';
        header("Location: admin/pages/admin.php"); // Path Admin Baru
        exit;
    }

    // 2. Cek User Biasa (Prepared Statement - AMAN)
    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['role'] = $row['role'];

            // Redirect ke Folder User Baru
            if ($row['role'] == 'admin') {
                header("Location: admin/pages/admin.php");
            } else {
                header("Location: user/pages/home.php"); 
            }
            exit;
        }
    }
    $error = "Email atau Password salah!";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sambal Bu Rudy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { burudy: { red: "#B91C1C", gold: "#F59E0B", dark: "#1F2937" } } } } };
    </script>
</head>
<body class="bg-gray-50 flex items-center justify-center h-screen font-sans">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border-t-4 border-burudy-red">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-burudy-dark">Selamat Datang Kembali</h1>
            <p class="text-gray-500 text-sm">Masuk untuk menikmati pedasnya Bu Rudy.</p>
        </div>

        <?php if(isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?= $error ?></span>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Email / Username</label>
                <input type="text" name="email" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-300 focus:outline-none focus:border-burudy-red transition">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-300 focus:outline-none focus:border-burudy-red transition">
            </div>
            <button type="submit" name="login" class="w-full bg-burudy-red text-white font-bold py-3 rounded-xl hover:bg-red-800 transition transform hover:scale-105 shadow-lg">
                Masuk Sekarang
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600">
            Belum punya akun? <a href="register.php" class="text-burudy-red font-bold hover:underline">Daftar disini</a>
        </p>
        <p class="mt-2 text-center text-xs text-gray-400">
            <a href="index.php" class="hover:text-gray-600">&larr; Kembali ke Beranda</a>
        </p>
    </div>
</body>
</html>