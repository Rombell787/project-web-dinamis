<!DOCTYPE html>
<html lang="en">
<?php session_start();
include 'koneksi.php';

if (isset($_GET['lihat_tabel'])) {
    $_SESSION['tabel_aktif'] = $_GET['lihat_tabel'];
}
?>

<head>
    <meta charset="UTF-8">
    <title>dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style-dashboard.css">
    <script src="script.js"></script>
    <script src="scriptTables.js"></script>
</head>

<body>
    <div class="container-nav">
        <div>
            <h1>Dashboard</h1>
            <form method="get" action="menu.php">
                <input type="hidden" name="aksi" value="cari">
                <input type="text" class="input-find" name="q" placeholder="Cari di semua data..." required>
                <button type="submit" class="button-top" onclick="menu('content1','cari')">Cari</button>
            </form>
        </div>
        <div class="container-nav-right">
            <button class="exit" name="exit" onclick="if (confirm('Apakah Anda yakin ingin keluar?')) {window.location.href = 'login.php';};">exit</button>
        </div>
    </div>
    <div class="container-top">
        <!-- menu start -->
        <div>
            <button onclick="menu('content1', 'tutup')" class="button-top">Tutup</button>
        </div>
        <div class="container-top-right">
            <button onclick="menu('content1', 'eksport')" class="button-top">eksport</button>
            <button onclick="menu('content1', 'import')" class="button-top">import</button>
            <button onclick="menu('content1', 'buat')" class="button-top">buat tabel</button>
            <button onclick="menu('content1', 'hapus')" class="button-top">hapus tabel</button>
        </div>
    </div>
    <div class="container">
        <div class="container-left">
            <?php
            $index = 2;
            $sql = "SHOW TABLES";
            $result = $koneksi->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_array()) {
                    $label = str_replace("_", " ", $row[0]);
                    $tableName = $row[0];
                    echo "<div class='role-box' style='cursor:pointer;' onclick=\"lihatTabel('$tableName')\">$label</div>";
                    $index++;
                }
            } else {
                echo "<div>Tidak ada tabel di database.</div>";
            }
            ?>
        </div>
        <!-- menu end -->
        <!-- content start -->
        <div id="content" class="container-right">
            <iframe id="kontenFrame" src="menu.php?aksi=" style="width: 100%; height: 200px; border: none;"></iframe>
            <div id="content1" class="content-downer">
                <div class="kontentabel" id="kontenTabel"></div>
            </div>
        </div>
        <!-- content end -->
    </div>
    <div class="container-bottom">
        <p>Content for the bottom section of the dashboard.</p>
    </div>
    <!-- Modal Popup -->
    <div id="popupModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close-btn" onclick="tutupPopup()">&times;</span>
            <div id="popupIsi"></div>
        </div>
    </div>

</body>

</html>