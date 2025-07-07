function lihatTabel(namaTabel) {
    fetch("lihat_tabel.php?tabel=" + namaTabel)
        .then(response => response.text())
        .then(html => {
            document.getElementById("kontenTabel").innerHTML = html;
        })
        .catch(err => {
            console.error("Gagal memuat tabel:", err);
        });
}