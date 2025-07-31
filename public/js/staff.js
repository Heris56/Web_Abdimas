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

document.addEventListener('DOMContentLoaded', function () {
    initSearchAndStatusFilter('#cariSiswa', '#statusFilter', '#table-data');
});

