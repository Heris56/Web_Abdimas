// notif untuk memberi tau status fungsi update
function showToast(message, className) {
    console.log("Toast:", message, className);
    const toastElement = document.getElementById("notificationToast");
    const toastBody = document.getElementById("toastMessage");

    // update isi notif
    toastBody.textContent = message;

    // ganti warna untuk notif sesuai status
    toastElement.classList.remove(
        "text-bg-primary",
        "text-bg-success",
        "text-bg-danger"
    );
    toastElement.classList.add(className);

    // tutup notif dalam 3 detik
    const toast = new bootstrap.Toast(toastElement, {
        delay: 3000,
    });
    toast.show();
}

// mengatur fungsi export ke excel
window.exportExcel = function (sheetName, fileName) {
    const table = document.getElementById("table-data");
    if (!table) {
        console.error("Table not found");
        window.showToast("Error: Table not found", "text-bg-danger");
        return;
    }

    const wb = XLSX.utils.table_to_book(table, {
        sheet: sheetName,
        raw: true,
    });
    XLSX.writeFile(wb, `${fileName}.xlsx`);
    window.showToast(`${sheetName} sukses tercetak!`, "text-bg-success");
};
