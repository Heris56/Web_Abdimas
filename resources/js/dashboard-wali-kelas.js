document.addEventListener('DOMContentLoaded', function () {
    const modalElement = document.getElementById('modal-presensi');
    const btnTrigger = document.getElementById('tombol-input-presensi');

    if (modalElement && btnTrigger) {
        const modal = new bootstrap.Modal(modalElement);
        btnTrigger.addEventListener('click', function () {
            modal.show();
        });
    } else {
        console.warn("Modal atau tombol tidak ditemukan di DOM.");
    }
});