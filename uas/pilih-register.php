<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Akun</title>
    <link rel="stylesheet" href="style/style-log-reg.css">
</head>
<body>
    <div class="main-box">
        <h2 style="text-align:center;">Pilih Registrasi Akun</h2>
        <div class="role-boxes">
            <a href="form-register-admin.php" data-action="admin" style="text-decoration:none; color:inherit;">
                <div class="role-box">
                    <div class="role-title">Admin</div>
                    <div class="role-desc">Daftar sebagai admin untuk mengelola sistem.</div>
                </div>
            </a>
            <a href="form-register-user.php" data-action="user" style="text-decoration:none; color:inherit;">
                <div class="role-box">
                    <div class="role-title">User</div>
                    <div class="role-desc">Daftar sebagai user untuk menggunakan layanan.</div>
                </div>
            </a>
            <a href="form-register-super-admin.php" data-action="super-admin" style="text-decoration:none; color:inherit;">
                <div class="role-box">
                    <div class="role-title">Super Admin</div>
                    <div class="role-desc">Daftar sebagai super admin dengan akses penuh.</div>
                </div>
            </a>
        </div>
    </div>
</body>
</html>