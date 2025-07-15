console.log("Hello from dashboard-wali-kelas");

document.getElementById('button-cetak').addEventListener('click', function () {
    alert('File presensi akan segera didownload');
});

function exportExcel() {
    const table = document.getElementById("table-presensi");
    const rows = table.querySelectorAll("tbody tr");
    const headers = ["Nama Siswa"];

    // Get date headers from thead (excluding first and last column)
    const ths = table.querySelectorAll("thead th");
    for (let i = 1; i < ths.length; i++) {
        headers.push(ths[i].textContent.trim());
    }

    const data = [headers];

    rows.forEach(row => {
        const cells = row.querySelectorAll("td");
        const rowData = [];

        // First cell is student name
        rowData.push(cells[0].textContent.trim());

        // Loop through select dropdowns
        for (let i = 1; i < cells.length; i++) {
            const select = cells[i].querySelector("select");
            const selectedText = select ? select.options[select.selectedIndex].text : "";
            rowData.push(selectedText === "-" ? "" : selectedText); // skip "-"
        }

        data.push(rowData);
    });

    const ws = XLSX.utils.aoa_to_sheet(data);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Presensi");
    XLSX.writeFile(wb, "presensi-siswa.xlsx");
}

function updateKehadiran(selectElement) {
    const nisn = selectElement.dataset.nisn;
    const tanggal = selectElement.dataset.tanggal;
    const keterangan_absen = selectElement.value;
    const tahun_ajaran = selectElement.dataset.tahun_ajaran;
    const editKehadiranURL = document.querySelector('meta[name="route-edit-kehadiran"]').getAttribute('content');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    console.log({ nisn, tanggal, keterangan_absen, tahun_ajaran });

    fetch(editKehadiranURL, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken
        },
        body: JSON.stringify({
            nisn: nisn,
            tanggal: tanggal,
            keterangan_absen: keterangan_absen,
            tahun_ajaran: tahun_ajaran
        })
    })
        .then(response => response.json())
        .then(data => {
            console.log("Update sukses:", data.message);
        })
        .catch(error => {
            console.error("Gagal update:", error);
        });
}

document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById("search_siswa");
    const rows = document.querySelectorAll("#table-presensi tbody tr");

    if (!input) return;

    input.addEventListener("keyup", function () {
        const inputSearch = this.value.toLowerCase();
        rows.forEach(row => {
            const td = row.querySelector("td");
            if (td) {
                const namaSiswa = td.textContent.toLowerCase();
                row.style.display = namaSiswa.includes(inputSearch) ? "" : "none";
            } else {
                row.style.display = "none";
            }
        });
    });
});

const buttons = document.querySelectorAll(".btn-delete-tanggal");
buttons.forEach(button => {
    button.addEventListener("click", function () {
        const tanggal = this.dataset.tanggal;

        if (!confirm(`Yakin ingin menghapus presensi untuk tanggal ${tanggal}?`)) return;

        fetch(`/dashboard/walikelas/delete_tanggal/${tanggal}`, {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert("Gagal menghapus data.");
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Terjadi kesalahan saat menghapus.");
            });
    });
});
