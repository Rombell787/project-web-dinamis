<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="scriptTables.php"></script>
    <link rel="stylesheet" href="style/table.css">
</head>
<style>
    @media (max-width: 768px) {
    body {
        font-family: Arial, sans-serif;
    }

    .tabel-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }
}
    </style>

<body>
    <?php session_start();
    include 'koneksi.php';
    error_reporting();

    $tabel = $_GET['tabel'] ?? ($_SESSION['tabel_aktif'] ?? '');
    $aksi  = $_GET['aksi'] ?? '';

    if (empty($tabel)) {
        exit("❌ Tabel belum dipilih. Akses lewat dashboard.php?lihat_tabel=nama_tabel");
    }

    $_SESSION['tabel_aktif'] = $tabel;

    $kolom = [];
    $sql = "DESCRIBE `$tabel`";
    $result = $koneksi->query($sql);
    while ($row = $result->fetch_assoc()) {
        $kolom[] = $row['Field'];
    }

    $tabel = preg_replace('/[^a-zA-Z0-9_]/', '', $tabel);
    $label = str_replace("_", " ", $tabel);

    $sql = "SELECT * FROM `$tabel`";
    $result = $koneksi->query($sql);
    ?>
    <!-- Tombol Tambah -->
    <button onclick="menu('content1', 'tambah_isi')">Tambah data</button>
    <?php
    if (!$result) {
        echo "Gagal mengambil data dari tabel: " . $koneksi->error;
        exit;
    }

    if ($result->num_rows > 0) {
        echo "<h3>Isi Tabel: $label</h3>";
        echo "<div class='tabel-wrapper'>";
        echo "<table border='1' cellpadding='8'>";
        echo "<tr>";

        // Nama kolom
        while ($field = $result->fetch_field()) {
            echo "<th>" . htmlspecialchars($field->name) . "</th>";
        }
        echo "<th>Aksi</th>";
        echo "</tr>";

        // Isi baris
        // Ambil tipe setiap kolom
        $tipe_kolom = [];
        $res_kol = $koneksi->query("DESCRIBE `$tabel`");
        while ($r = $res_kol->fetch_assoc()) {
            $tipe_kolom[$r['Field']] = strtolower($r['Type']);
        }

        // Isi baris
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $kol => $cell) {
                $tipe = $tipe_kolom[$kol] ?? '';

                if ($kol == 'deskripsi_html') {
                    echo "<td>" . $cell . "</td>"; // tampilkan apa adanya
                } else {
                    echo "<td>" . htmlspecialchars($cell) . "</td>"; // aman
                }
            }

            echo "<td>";
            echo "<button onclick=\"menu('content1', 'edit_isi')\">Edit</button> ";
            echo "<button onclick=\"menu('content1', 'hapus_isi')\">Hapus</button>";
            echo "</td>";
            echo "</tr>";
        }

        echo "</table>";
        echo "</div>";
    } else {
        echo "<p>Tabel kosong.</p>";
    }
    ?>

</body>
    <script src="script.js"></script>
</html>