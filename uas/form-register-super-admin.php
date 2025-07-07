<?php
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $alamat = trim($_POST['alamat'] ?? '');
    $db_name = trim($_POST['db_name'] ?? '');
    $buat_tabel = isset($_POST['buat_tabel']);


    if ($username === '' || $password === '' || $confirm_password === '' || $alamat === '' || $db_name === '') {
    } elseif ($password !== $confirm_password) {
        $message = "<script> alert ('Konfirmasi password tidak sama')</script>";
    } else {
        //MySQL
        $koneksi = new mysqli('localhost', 'root', '');
        if ($koneksi->connect_error) {
            $message = "<script> alert ('Koneksi ke MySQL gagal: .$koneksi->connect_error');";
        } else {
            // Create database
            if ($koneksi->query("CREATE DATABASE IF NOT EXISTS `$db_name`")) {
                $message = "<script> alert ('Database berhasil dibuat tetapi anda telah mempunyai file koneksi.php dan sekarang akan diperbaharui cek bagian db koneksi.php untuk meninjau lebih lanjut');</script>;";
                if ($buat_tabel) {
                    $koneksi->select_db($db_name);
                    $sql = "CREATE TABLE IF NOT EXISTS `user_register` (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        username VARCHAR(100) NOT NULL,
                        password VARCHAR(255) NOT NULL,
                        alamat VARCHAR(255) NOT NULL,
                        db VARCHAR(50) NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    );";
                    // Create table relasi
                    $sql .= "CREATE TABLE IF NOT EXISTS `db` (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        role VARCHAR(50) NOT NULL UNIQUE
                    );";

                    $sql = "INSERT INTO db (role) VALUES ('admin'), ('user');";
;

                    if ($koneksi->query($sql)) {
                        //user data
                        $start = $koneksi->prepare("INSERT INTO `user_register` (username, password, alamat) VALUES (?, ?, ?)");
                        $start->bind_param("sss", $username, $password, $alamat);
                        if ($start->execute()) {
                            $message = "<script> alert ('Registrasi dan pembuatan tabel berhasil tetapi anda telah mempunyai file koneksi.php dan sekarang akan diperbaharui cek bagian db koneksi.php untuk meninjau lebih lanjut');                          
                                            let konfirmasi = confirm('Apakah kamu ingin pindah ke halaman dashboard?');
                                            if (konfirmasi) {
                                                window.location.href = 'dashboard.php'; // Ganti dengan file tujuanmu
                                            }
                                        </script>";
                            // Create koneksi.php file
                            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                $db_name = trim($_POST['db_name'] ?? '');
                                "<script> alert ('Registrasi berhasil, pembuatan tabel berhasil, File koneksi.php berhasil dibuat');</script>";
                                if ($db_name !== '') {
                                    $koneksi_file = __DIR__ . '/koneksi.php';
                                    $koneksi_data = "<?php\n\$koneksi = new mysqli('localhost', 'root', '', '$db_name');\nif (\$koneksi->connect_error) {\n    die('Koneksi gagal: ' . \$koneksi->connect_error);\n}\n?>\n";
                                    file_put_contents($koneksi_file, $koneksi_data);
                                }
                            } else {
                                $message = "<script> alert ('Gagal membuat file koneksi.php.');</script>";
                            }
                        } else {
                            $message = "<script> alert ('Gagal menyimpan data user: .$koneksi->connect_error');</script>";
                        }
                        $start->close();
                    } else {
                        $message = "<script> alert ('Gagal membuat tabel data: .$koneksi->connect_error');</script>";
                    }
                }
            } else {
                $message = "<script> alert ('Gagal membuat database: .$koneksi->connect_error');</script>";
            }
            $koneksi->close();
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Registrasi Super Admin</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style-log-reg.css">
    <script src="script.js"></script>
</head>

<body>
    <div class="register-box">
        <h2>Registrasi Super Admin</h2>
        <?php if ($message) echo $message; ?>
        <form method="post" onsubmit="validateForm();">
            <a for="username">Username</a>
            <input type="text" name="username" id="username" required>
            <a for="password">Password</a>
            <input type="password" name="password" id="password" required>
            <a for="confirm_password">Konfirmasi Password</a>
            <input type="password" name="confirm_password" id="confirm_password" required>
            <a for="alamat">Alamat</a>
            <input type="text" name="alamat" id="alamat" required>
            <a for="db_name">Nama Database</a>
            <input type="text" name="db_name" id="db_name" required>

            <a class="checkbox-a">
                <input type="checkbox" name="buat_tabel" id="buat_tabel" checked required>
                Buat tabel data login
            </a>
            <br>
            <a class="checkbox-a">
                <input type="checkbox" name="buat_koneksi" id="buat_koneksi" checked required>
                Buat koneksi
            </a>
            <br><br>
            <button type="submit">Register</button>
        </form>
    </div>
</body>

</html>