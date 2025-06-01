// Set header supaya ga perlu manggil _token lagi untuk AJAX operation
$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

$(document).ready(function () {
    // untuk menangani request form input nilai
    $('#inputNilaiForm').on('submit', function (e) {
        e.preventDefault();
        var nisn = $('#nisnSelect').val();
        var kegiatan = $('#kegiatanSelect').val();
        var nilai = $('#nilaiInput').val();
        var mapel = $('#mapelSelect').val();
        var tahun = $('#tahunSelect').val();
        var kelas = $('#kelasFilter').val() || '';

        if (!nisn || !kegiatan || !nilai || !mapel || !tahun) {
            showToast('Harap isi semua field!', 'text-bg-danger');
            return;
        }

        showToast('Sedang menyimpan...', 'text-bg-primary');

        $.ajax({
            url: "/dashboard/guru-mapel/input-nilai",
            type: "POST",
            data: {
                nisn: nisn,
                kegiatan: kegiatan,
                nilai: nilai,
                mapel: mapel,
                tahun_pelajaran: tahun,
                id_kelas: kelas
            },
            success: function (response) {
                console.log('Input nilai success:', response);
                showToast('Nilai berhasil disimpan!', 'text-bg-success');
                $('#inputNilaiModal').modal('hide');
                $('#inputNilaiForm')[0].reset();
                fetchFilteredData($('#mapelFilter').val() || '', $('#tahunFilter').val() || '', kelas);
            },
            error: function (xhr, status, error) {
                console.error('Input nilai error:', { status, error, responseText: xhr.responseText });
                let message = xhr.responseJSON?.message || 'Gagal menyimpan nilai!';
                showToast(message, 'text-bg-danger');
            }
        });
    });

    // ganti ganti filter
    $("#mapelFilter, #tahunFilter, #kelasFilter").on("change", function () {
        var mapel = $("#mapelFilter").val() || "";
        var tahun = $("#tahunFilter").val() || "";
        var kelas = $("#kelasFilter").val() || "";
        console.log("Filter change triggered:", {
            mapel: mapel,
            tahun_pelajaran: tahun,
            id_kelas: kelas,
        });
        fetchFilteredData(mapel, tahun, kelas);
    });

    function fetchFilteredData(mapel, tahun, kelas) {
        console.log("Fetching data:", {
            mapel: mapel,
            tahun_pelajaran: tahun,
            id_kelas: kelas,
        });
        $.ajax({
            url: "/dashboard/guru-mapel",
            type: "GET",
            data: {
                mapel: mapel,
                tahun_pelajaran: tahun,
                id_kelas: kelas,
            },
            success: function (response) {
                console.log("AJAX success:", response);
                updateTable(response);
                // Biar filternya tetap
                $("#mapelFilter").val(mapel);
                $("#tahunFilter").val(tahun);
                $("#kelasFilter").val(kelas);
            },
            error: function (xhr, status, error) {
                console.error("Filter error:", {
                    status,
                    error,
                    responseText: xhr.responseText,
                });
                showToast(
                    "Failed to load data: " +
                        (xhr.responseJSON?.message || error),
                    "text-bg-danger"
                );
            },
        });
    }

    function updateTable(data) {
        console.log("updateTable data:", data);
        var $tableContainer = $("#tableContainer");
        $tableContainer.empty();

        // Update header table dengan nilai baru
        var headerHtml =
            data.data_nilai.length > 0
                ? `<div class="header mb-2 mt-2"><span class="head">${data.nama_mapel}</span></div>`
                : '<div class="alert alert-danger mt-4">Tidak ada data yang tersedia!</div>';

        // Update table dengan nilai baru
        var tableHtml = `
            <table class="table table-bordered" id="nilaiTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Tahun Ajaran</th>
                        ${data.kegiatanList
                            .map((kegiatan) => `<th>${kegiatan}</th>`)
                            .join("")}
                    </tr>
                </thead>
                <tbody>
                    ${data.data_nilai
                        .map(
                            (row, i) => `
                        <tr>
                            <td>${i + 1}</td>
                            <td>${row.nisn}</td>
                            <td>${row.nama_siswa}</td>
                            <td>${row.id_kelas}</td>
                            <td>${row.tahun_pelajaran}</td>
                            ${data.kegiatanList
                                .map((kegiatan) => {
                                    var alias = kegiatan
                                        .replace(/\s+/g, "_")
                                        .toLowerCase();
                                    return `<td class="editable" data-nisn="${
                                        row.nisn
                                    }" data-field="${kegiatan}">${
                                        row[alias] ?? "-"
                                    }</td>`;
                                })
                                .join("")}
                        </tr>
                    `
                        )
                        .join("")}
                </tbody>
            </table>
        `;

        $tableContainer.html(headerHtml + tableHtml);
        attachEditableListeners();
    }

    // Function agar cellnya clickable
    function attachEditableListeners() {
        $(".editable").on("click", function () {
            var $cell = $(this);
            var nisn = $cell.data("nisn");
            var field = $cell.data("field");
            var currentValue = $cell.text() === "-" ? "" : $cell.text();

            $cell.html(
                `<input type="text" value="${currentValue}" class="form-control form-control-sm">`
            );
            var $input = $cell.find("input");
            $input.focus();

            $input.on("blur keypress", function (e) {
                if (e.type === "blur" || e.which === 13) {
                    saveValue($cell, $input, nisn, field);
                }
            });
        });
    }

    // simpan value yang sudah terupdate
    function saveValue($cell, $input, nisn, field) {
        var newValue = $input.val().trim() || "-";
        $cell.text(newValue);
        $input.remove();

        console.log("saveValue inputs:", {
            nisn: nisn,
            field: field,
            value: newValue,
            nisn_type: typeof nisn,
            field_type: typeof field,
        });

        showToast("Sedang menyimpan...", "text-bg-primary");

        $.ajax({
            url: "/dashboard/guru-mapel/update-nilai",
            type: "POST",
            data: {
                nisn: nisn,
                field: field,
                value: newValue === "-" ? null : newValue,
            },
            success: function (response) {
                console.log("AJAX success:", response);
                showToast("Sukses Update Data!", "text-bg-success");
                fetchFilteredData(
                    $("#mapelFilter").val() || "",
                    $("#tahunFilter").val() || "",
                    $("#kelasFilter").val() || ""
                );
                window.location.reload();
            },
            error: function (xhr, status, error) {
                console.error("AJAX error:", {
                    status,
                    error,
                    responseText: xhr.responseText,
                });
                let message = xhr.responseJSON?.message || "Gagal Update Data!";
                showToast(message, "text-bg-danger");
            },
        });
    }

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

    attachEditableListeners();
});
