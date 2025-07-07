<?php
include 'koneksi.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $password = $_POST['password'] ?? ''; //MD5 belum diset ini hanya demo

    // Ambil id dari role 'user' di tabel db
    $result = $koneksi->query("SELECT role FROM db WHERE id = '2' LIMIT 1");
    if ($row = $result->fetch_assoc()) {
        $user_role = $row['role'];

        // Simpan ke tabel user_register
        $start = $koneksi->prepare("INSERT INTO `user_register` (username, password, alamat, role) VALUES (?, ?, ?, ?)");
                        $start->bind_param("ssss", $username, $password, $alamat, $user_role);

        if ($start->execute()) {
            $message = "<script>alert('Registrasi berhasil!'); window.location.href='login.php';</script>";
        } else {
            $message = "<script>alert('Gagal menyimpan data: {$start->error}');</script>";
        }

        $start->close();
    } else {
        $message = "<script>alert('Role user tidak ditemukan di tabel db');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Form Registrasi User</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style-log-reg.css">
</head>
<body>
    <div class="register-box">
        <h2>Form Registrasi User</h2>
        <?= $message ?>
        <form method="post">
            <input type="text" name="username" id="username" placeholder="username" required>

            <input type="text" name="alamat" id="alamat" placeholder="alamat" required>

            <input type="password" name="password" id="password" placeholder="password" required>

            <button type="submit">Daftar</button>
        </form>
    </div>
</body>
</html>
