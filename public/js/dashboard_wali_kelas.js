document.getElementById('button-cetak').addEventListener('click', function () {
    alert('File presensi akan segera didownload');
});

function exportExcel() {
    const table = document.getElementById("table-presensi");
    const rows = table.querySelectorAll("tbody tr");
    const headers = ["Nama Siswa"];

    // Get date headers from thead (excluding first and last column)
    const ths = table.querySelectorAll("thead th");
    for (let i = 1; i < ths.length - 1; i++) {
        headers.push(ths[i].textContent.trim());
    }

    const data = [headers];

    rows.forEach(row => {
        const cells = row.querySelectorAll("td");
        const rowData = [];

        // First cell is student name
        rowData.push(cells[0].textContent.trim());

        // Loop through select dropdowns
        for (let i = 1; i < cells.length - 1; i++) {
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

    fetch("{{ route('dashboard.walikelas.edit-kehadiran') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            nisn: nisn,
            tanggal: tanggal,
            keterangan_absen: keterangan_absen
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