<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="script.js"></script>
    <link rel="stylesheet" href="style/menu.css">
</head>

<body>

    <?php session_start();
    include 'koneksi.php';
    error_reporting();

    $tabel = $_GET['tabel'] ?? ($_SESSION['tabel_aktif'] ?? '');
    $aksi  = $_GET['aksi'] ?? '';
    $q = $_GET['q'] ?? '';

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

    if ($aksi == 'buat') {
    ?>
        <form method="post" action="?aksi=buat">
            Nama Tabel: <input type="text" name="nama_tabel" placeholder="masukkan nama" required><br><br>

            <div id="kolom-container">
                <!-- Kolom pertama -->
                <div class="kolom">
                    Nama Kolom: <input type="text" name="kolom[0][nama]" required>
                    Tipe:
                    <select name="kolom[0][tipe]" required>
                        <option value="INT">INT</option>
                        <option value="VARCHAR">VARCHAR</option>
                        <!-- <option value="FILE_IMAGE">FILE IMAGE</option>
                    <option value="FILE_VIDEO">FILE VIDEO</option> -->
                    </select>
                </div>
            </div>

            <button type="button" onclick="tambahKolom()">+ Tambah Kolom</button>
            <br><br>
            <input type="submit" name="submit" value="Buat Tabel">
        </form>
        <hr>
        <?php

        // PROSES
        if (isset($_POST['submit'])) {
            $nama_tabel = $_POST['nama_tabel'] ?? '';
            $label = str_replace(" ", "_", $nama_tabel);
            $kolom = $_POST['kolom'] ?? [];

            if (empty($kolom)) {
                echo "Tidak ada kolom yang dikirim.";
                exit;
            }

            $sql = "CREATE TABLE `$label` (
            id INT AUTO_INCREMENT PRIMARY KEY, ";

            foreach ($kolom as $k) {
                $nama = str_replace(' ', '_', $k['nama']);
                $tipe = $k['tipe'];

                if ($tipe === 'FILE_IMAGE' || $tipe === 'FILE_VIDEO') {
                    $tipe_sql = "MEDIUMBLOB";
                } elseif ($tipe === 'INT') {
                    $tipe_sql = "INT";
                } elseif ($tipe === 'VARCHAR') {
                    $tipe_sql = "VARCHAR(255)";
                }

                $sql .= "`$nama` $tipe_sql NOT NULL, ";
            }

            $sql .= "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

            if ($koneksi->query($sql) === TRUE) {
                echo "Tabel '$label' berhasil dibuat.";
            } else {
                echo "Error: " . $koneksi->error;
            }
        }
    } elseif ($aksi == 'hapus') {
        echo "<h3>Hapus Tabel</h3>";

        // Proses penghapusan tabel
        if (isset($_POST['hapus_tabel'])) {
            $tabel_hapus = $_POST['tabel_hapus'];
            $sql = "DROP TABLE `$tabel_hapus`";

            if ($koneksi->query($sql) === TRUE) {
                echo "<p style='color:green;'>Tabel <b>$tabel_hapus</b> berhasil dihapus.</p>";
            } else {
                echo "<p style='color:red;'>Gagal menghapus tabel: " . $koneksi->error . "</p>";
            }
        }

        // Tampilkan daftar tabel
        $sql = "SHOW TABLES";
        $result = $koneksi->query($sql);

        if ($result->num_rows > 0) {
            echo "<form method='post' onsubmit=\"return confirm('Yakin ingin menghapus tabel ini?');\">";
            echo "<label>Pilih Tabel yang Akan Dihapus:</label><br>";
            echo "<select name='tabel_hapus' required>";
            while ($row = $result->fetch_array()) {
                echo "<option value='" . $row[0] . "'>" . $row[0] . "</option>";
            }
            echo "</select><br><br>";
            echo "<input type='submit' name='hapus_tabel' value='Hapus Tabel' style='background:red; color:white;'>";
            echo "</form>";
        } else {
            echo "<p>Tidak ada tabel untuk dihapus.</p>";
        }
    } elseif ($aksi == 'eksport') {
    if (!isset($_POST['tabel_export'])) {
        echo "<h3>Export Data ke Excel</h3>";
        echo "<form method='post'>";
        echo "<label>Pilih Tabel:</label> ";
        echo "<select name='tabel_export' required>";

        $sql = "SHOW TABLES";
        $result = $koneksi->query($sql);
        while ($row = $result->fetch_array()) {
            $nama = $row[0];
            echo "<option value='$nama'>$nama</option>";
        }

        echo "</select> ";
        echo "<button type='submit'>Export</button>";
        echo "</form>";
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tabel_export'])) {
        $tabel = $_POST['tabel_export'];

        // Ambil kolom
        $kolom = [];
        $res = $koneksi->query("SHOW COLUMNS FROM `$tabel`");
        while ($r = $res->fetch_assoc()) {
            $kolom[] = $r['Field'];
        }

        // Set header Excel
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$tabel.xls");

        $hasil = $koneksi->query("SELECT * FROM `$tabel`");

        echo "<table border='1'>";
        // Header kolom
        echo "<tr>";
        foreach ($kolom as $k) {
            echo "<th>" . htmlspecialchars($k) . "</th>";
        }
        echo "</tr>";

        // Data
        while ($row = $hasil->fetch_assoc()) {
            echo "<tr>";
            foreach ($kolom as $k) {
                echo "<td>" . htmlspecialchars($row[$k]) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        exit;
    }
} elseif ($aksi == 'import') {
    ?>
    <h3>Import File CSV dan Buat Tabel Otomatis</h3>
    <form method="post" enctype="multipart/form-data">
        <label>Nama Tabel Baru:</label><br>
        <input type="text" name="nama_tabel" required placeholder="masukkan nama tabel"><br><br>
        <label>Pilih File CSV:</label><br>
        <input type="file" name="file_csv" accept=".csv" required><br><br>
        <button type="submit" name="import_csv">Import</button>
    </form>
    <?php
    if (isset($_POST['import_csv'])) {
    $nama_tabel = preg_replace('/[^a-zA-Z0-9_]/', '_', $_POST['nama_tabel']); // amankan nama tabel
    $file = $_FILES['file_csv']['tmp_name'];

    if (($handle = fopen($file, "r")) !== FALSE) {
        $header = fgetcsv($handle, 1000, ",");

        // Buat query CREATE TABLE
        $sql_create = "CREATE TABLE IF NOT EXISTS `$nama_tabel` (
            id INT AUTO_INCREMENT PRIMARY KEY,";

        foreach ($header as $kolom) {
            $kolom = mysqli_real_escape_string($koneksi, trim($kolom));
            $sql_create .= "`$kolom` TEXT,";
        }

        $sql_create = rtrim($sql_create, ',') . ')';
        if (!mysqli_query($koneksi, $sql_create)) {
            echo "Gagal membuat tabel: " . mysqli_error($koneksi);
            exit;
        }

        // Masukkan data baris per baris
        $berhasil = 0;
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $values = [];
            foreach ($data as $value) {
                $values[] = "'" . mysqli_real_escape_string($koneksi, $value) . "'";
            }

            $kolom_str = '`' . implode('`,`', $header) . '`';
            $nilai_str = implode(',', $values);

            $sql_insert = "INSERT INTO `$nama_tabel` ($kolom_str) VALUES ($nilai_str)";
            if (mysqli_query($koneksi, $sql_insert)) {
                $berhasil++;
            }
        }

        fclose($handle);
        echo "Import selesai. Tabel <b>$nama_tabel</b> berhasil dibuat dan diisi dengan <b>$berhasil</b> data.";
    } else {
        echo "Gagal membuka file CSV.";
    }
}
} elseif ($aksi == 'tambah_isi') {
        // ambil struktur kolom
        $kolom = [];
        $res = $koneksi->query("DESCRIBE `$tabel`");
        while ($r = $res->fetch_assoc()) {
            $kolom[] = $r;
        }

        // proses jika form disubmit
        if (isset($_POST['submit_tambah'])) {
            $fields = [];
            $values = [];

            foreach ($kolom as $k) {
                $nama = $k['Field'];
                $tipe = strtolower($k['Type']);
                if (in_array($nama, ['id', 'created_at'])) continue;

                $fields[] = "`$nama`";

                if (strpos($tipe, 'BLOB') !== false) {
                    $fileTmp = $_FILES["data_$nama"]['tmp_name'] ?? '';
                    if ($fileTmp && file_exists($fileTmp)) {
                        $data = file_get_contents($fileTmp);
                        $data = $koneksi->real_escape_string($data);
                        $values[] = "'$data'";
                    } else {
                        $values[] = "NULL";
                    }
                } elseif (strpos($tipe, 'INT') !== false) {
                    $val = trim($_POST["data_$nama"] ?? '');
                    $values[] = $val === '' ? "NULL" : intval($val);
                } else {
                    $val = $koneksi->real_escape_string($_POST["data_$nama"] ?? '');
                    $values[] = "'$val'";
                }
            }

            $sql = "INSERT INTO `$tabel` (" . implode(",", $fields) . ")
                VALUES (" . implode(",", $values) . ")";
            if ($koneksi->query($sql)) {
                echo "<p style='color:green;'>Data berhasil ditambahkan ke <b>$tabel</b>.</p>";
            } else {
                echo "<p style='color:red;'>Gagal menambah data: " . $koneksi->error . "</p>";
            }
        }

        // tetap ditampilkan setelah submit
        echo "<h3>Tambah Data ke Tabel $tabel</h3>";
        echo "<form method='post' enctype='multipart/form-data'>";
        foreach ($kolom as $k) {
            $nama = $k['Field'];
            $tipe = strtolower($k['Type']);

            if (in_array($nama, ['id', 'created_at'])) continue;

            if (strpos($tipe, 'int') !== false) {
                echo "$nama: <input type='number' name='data_$nama'><br>";
            } elseif (strpos($tipe, 'varchar') !== false || strpos($tipe, 'TEXT') !== false) {
                echo "$nama: <input type='text' name='data_$nama'><br>";
            } elseif (strpos($tipe, 'mediumblob') !== false) {
                echo "$nama: <input type='file' name='data_$nama'><br>";
            } else {
                echo "$nama: <input type='text' name='data_$nama'><br>"; // fallback
            }
        }

        echo "<button type='submit' name='submit_tambah'>Tambah</button>";
        echo "</form>";
    } elseif ($aksi == 'edit_isi') {
        echo "<h3>Edit Data di $tabel</h3>";
        echo "<form method='post'>";
        echo "ID (baris yang ingin diedit): <input type='text' name='id' required><br><br>";
        foreach ($kolom as $k) {

            if (in_array($k, ['id', 'created_at'])) continue;
            echo "$k: <input type='text' name='data[$k]'><br>";
        }
        echo "<button type='submit' name='submit_edit'>Edit</button>";
        echo "</form>";

        if (isset($_POST['submit_edit'])) {
            $id = $koneksi->real_escape_string($_POST['id']);
            $data = $_POST['data'];

            $set = [];
            foreach ($data as $kol => $val) {
                $val = $koneksi->real_escape_string($val);
                $set[] = "`$kol` = '$val'";
            }
            $set_sql = implode(", ", $set);


            // kolom pertama 0
            $kolom_id = $kolom[0];

            $sql = "UPDATE `$tabel` SET $set_sql WHERE `$kolom_id` = '$id'";

            if ($koneksi->query($sql)) {
                echo "Data berhasil diedit.";
            } else {
                echo "Gagal mengedit data: " . $koneksi->error;
            }
        }
    } elseif ($aksi == 'hapus_isi') {
        echo "<h3>Hapus Data dari $tabel</h3>";
        echo "<form method='post'>";
        echo "ID (baris yang ingin dihapus): <input type='text' name='id' required><br><br>";
        echo "<button type='submit' name='submit_hapus'>Hapus</button>";
        echo "</form>";

        if (isset($_POST['submit_hapus'])) {
            $id = $koneksi->real_escape_string($_POST['id']);

            // kolom pertama 0 
            $kolom_id = $kolom[0];

            $sql = "DELETE FROM `$tabel` WHERE `$kolom_id` = '$id'";

            if ($koneksi->query($sql)) {
                echo "Data berhasil dihapus.";
            } else {
                echo "Gagal menghapus data: " . $koneksi->error;
            }
        }
    } elseif ($aksi == 'cari' && $q != '') {
        echo "<h2>Hasil Pencarian: " . htmlspecialchars($q) . "</h2>";

        $tables = mysqli_query($koneksi, "SHOW TABLES");
        while ($tableRow = mysqli_fetch_row($tables)) {
            $tabel = $tableRow[0];

            // Ambil kolom teks dari tabel
            $columns = mysqli_query($koneksi, "SHOW COLUMNS FROM `$tabel`");
            $textCols = [];
            while ($col = mysqli_fetch_assoc($columns)) {
                if (preg_match('/char|text/i', $col['Type'])) {
                    $textCols[] = $col['Field'];
                }
            }

            // Cek tiap kolom teks
            foreach ($textCols as $colName) {
                $query = "SELECT * FROM `$tabel` WHERE `$colName` LIKE '%$q%' LIMIT 5";
                $result = mysqli_query($koneksi, $query);
                if (mysqli_num_rows($result) > 0) {
                    echo "<h3> Tabel $tabel — Kolom $colName</h3>";
                    echo "<table border='1' cellpadding='5'><tr>";
                    $fields = mysqli_fetch_fields($result);
                    foreach ($fields as $field) {
                        echo "<th>{$field->name}</th>";
                    }
                    echo "</tr>";
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        foreach ($fields as $field) {
                            echo "<td>" . htmlspecialchars($row[$field->name]) . "</td>";
                        }
                        echo "</tr>";
                    }
                    echo "</table><br>";
                }
            }
        }

        exit;
    } elseif ($aksi == 'tutup') {
        exit;
    } else {
    }
    ?>


</body>

</html>