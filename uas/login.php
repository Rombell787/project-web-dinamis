<?php
include 'koneksi.php';
session_start();
$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek ke database
    $query = "SELECT * FROM user_register WHERE username='$username' AND password='$password'";
    $result = mysqli_query($koneksi, $query);
        if (mysqli_num_rows($result) === 1) {
            $_SESSION['username'] = $username;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Username atau password salah!";
        } 
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login User</title>
    <link rel="stylesheet" href="style/style-log-reg.css">
    
</head>
<body>
    <div class="login-box">
        <h2>Login User</h2>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" autocomplete="off">
            <input type="text" name="username" placeholder="Username" required autofocus>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" name="login" value="Login">
            <p>belum punya akun?,<a href="pilih-register.php">register</a> disini</p>
        </form>
    </div>
</body>
</html>