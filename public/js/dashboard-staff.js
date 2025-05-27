document.getElementById("button-cetak").addEventListener("click", function () {
    showToast("Mencetak {{ $buttonText }}...", "text-bg-primary");
    exportExcel();
});

document.querySelectorAll("[data-export-url]").forEach((button) => {
    button.addEventListener("click", function () {
        const url = this.getAttribute("data-export-url");
        window.location.href = url;
    });
});

// mengatur fungsi export ke excel
function exportExcel() {
    const table = document.getElementById("table-data");
    const wb = XLSX.utils.table_to_book(table, {
        sheet: "{{ $buttonText }}",
        raw: true,
    });
    XLSX.writeFile(
        wb,
        "{{ str_replace(' ', '_', $buttonText) }}_{{ date('YmdHis') }}.xlsx"
    );
    showToast("{{ $buttonText }} sukses tercetak!", "text-bg-success");
}
