// form register super admin start
function validateForm() {
    var pass = document.getElementById('password').value;
    var conf = document.getElementById('confirm_password').value;
    if (pass !== conf) {
        alert('Konfirmasi password harus sama.');
        return false;
    }
    return true;
}
// form register super admin end
// dashboard start
// welcom akan aktif saat masuk (antisipasi bug)
window.addEventListener('DOMContentLoaded', function () {
    menu('content0');
});


function menu(id, aksi = null) {
    // Sembunyikan semua konten
    const semuaKonten = document.querySelectorAll('.content');
    semuaKonten.forEach(konten => konten.style.display = 'none');

    // Tampilkan konten yang sesuai ID
    const target = document.getElementById(id);
    if (target) {
        target.style.display = 'block';
    }

    // Jika id = content1 dan ada aksi, arahkan iframe
    if (id === 'content1' && aksi) {
        document.getElementById('kontenFrame').src = 'menu.php?aksi=' + aksi;
        
    }
}
function lihatTabel(nama) {
    document.getElementById('kontenFrame').src = 'menu.php?tabel=' + nama;
}

// dashboard end
// menu tambah start
let index = 1;
function tambahKolom() {
    const container = document.getElementById("kolom-container");
    const kolom = document.createElement("div");
    kolom.classList.add("kolom");

    kolom.innerHTML = `
        Nama Kolom: <input type="text" name="kolom[${index}][nama]" required>
        Tipe:
        <select name="kolom[${index}][tipe]" required>
            <option value="INT">INT</option>
            <option value="VARCHAR">VARCHAR</option>
            <option value="FILE_IMAGE">FILE IMAGE</option>
             <option value="FILE_VIDEO">FILE VIDEO</option>
        </select>
    `;
    container.appendChild(kolom);
    index++;
}

function togglePanjang(select) {
    const panjangInput = select.parentElement.querySelector('');
    if (select.value === "VARCHAR" || select.value.includes("FILE")) {
        panjangInput.disabled = false;
    } else {
        panjangInput.disabled = true;
        panjangInput.value = "";
    }
}

// menu tambah end
// menu end