// staff.js
function initSiswaTableSearch(inputSelector, tableSelector) {
    const input = document.querySelector(inputSelector);
    if (!input) return; // kalau input gak ada, skip

    input.addEventListener('keyup', function () {
        const filter = input.value.toLowerCase();
        const rows = document.querySelectorAll(`${tableSelector} tbody tr`);

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
}


document.addEventListener('DOMContentLoaded', function () {
    initSiswaTableSearch('#cariSiswa', '#table-data');
});
