<?php
$koneksi = new mysqli('localhost', 'root', '', 'one');
if ($koneksi->connect_error) {
    die('Koneksi gagal: ' . $koneksi->connect_error);
}
?>
