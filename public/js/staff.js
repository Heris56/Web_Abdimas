function initSearchAndStatusFilter(searchSelector, filterSelector, tableSelector) {
    const input = document.querySelector(searchSelector);
    const filter = document.querySelector(filterSelector);

    if (!input || !filter) return;

    const doFilter = () => {
        const searchText = input.value.toLowerCase();
        const statusFilter = filter.value.toLowerCase();

        const rows = document.querySelectorAll(`${tableSelector} tbody tr`);

        rows.forEach(row => {
            const rowText = row.textContent.toLowerCase();

            // cari cell yg punya data-status
            const statusTd = row.querySelector('[data-status]');
            const statusText = statusTd ? statusTd.getAttribute('data-status') : '';

            const matchesSearch = rowText.includes(searchText);
            const matchesStatus = statusFilter ? statusText === statusFilter : true;

            if (matchesSearch && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    };

    input.addEventListener('keyup', doFilter);
    filter.addEventListener('change', doFilter);
}

function initFilterPaketMapel(searchSelector, filterSelector, tableSelector) {
    const input = document.querySelector(searchSelector);
    const filter = document.querySelector(filterSelector);

    if (!input || !filter) return;

    const doFilter = () => {
        const searchText = input.value.toLowerCase();
        const selectedKodePaket = filter.value.toLowerCase();

        const rows = document.querySelectorAll(`${tableSelector} tbody tr`);

        rows.forEach(row => {
            const rowText = row.textContent.toLowerCase();

            const kodePaketCell = row.children[1]; // Ambil kolom kode_paket
            const rowKodePaket = kodePaketCell ? kodePaketCell.textContent.trim().toLowerCase() : '';

            const matchesSearch = rowText.includes(searchText);
            const matchesKodePaket = selectedKodePaket ? rowKodePaket === selectedKodePaket : true;

            if (matchesSearch && matchesKodePaket) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    };

    input.addEventListener('keyup', doFilter);
    filter.addEventListener('change', doFilter);
}

function initTableSearchOnly(searchSelector, tableSelector) {
    const input = document.querySelector(searchSelector);

    if (!input) return;

    const doSearch = () => {
        const searchText = input.value.toLowerCase();
        const rows = document.querySelectorAll(`${tableSelector} tbody tr`);

        rows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            const matchesSearch = rowText.includes(searchText);

            row.style.display = matchesSearch ? '' : 'none';
        });
    };

    input.addEventListener('keyup', doSearch);
}

document.addEventListener('DOMContentLoaded', function () {
    initSearchAndStatusFilter('#cariSiswa', '#statusFilter', '#table-data');
    initFilterPaketMapel("#cariSiswa", '#filterKodePaket', '#table-data')
    initTableSearchOnly('#cariSiswa', '#table-data');
});

