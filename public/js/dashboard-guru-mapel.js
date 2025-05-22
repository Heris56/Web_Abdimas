// Set header supaya ga perlu manggil _token lagi untuk AJAX operation
$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

$(document).ready(function () {
    // Handle mapel filter change
    $('#mapelFilter').on('change', function () {
        var mapel = $(this).val();
        fetchFilteredData(mapel);
    });

    // Fetch filtered data
    function fetchFilteredData(mapel) {
        $.ajax({
            url: "/dashboard/guru-mapel",
            type: "GET",
            data: { mapel: mapel },
            success: function (response) {
                updateTable(response);
            },
            error: function (xhr, status, error) {
                console.error('Filter error:', { status, error, responseText: xhr.responseText });
                showToast("Failed to load data!", "text-bg-danger");
            }
        });
    }

    // Update table with new data
    function updateTable(data) {
        var $tableContainer = $('#tableContainer');
        $tableContainer.empty();

        // Update header
        var headerHtml = data.data_nilai.length > 0
            ? `<div class="header mb-2"><span class="head">${data.nama_mapel}</span></div>`
            : '';

        // Build table
        var tableHtml = `
            <table class="table table-bordered" id="nilaiTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Tahun Ajaran</th>
                        ${data.kegiatanList.map(kegiatan => `<th>${kegiatan}</th>`).join('')}
                    </tr>
                </thead>
                <tbody>
                    ${data.data_nilai.map((row, i) => `
                        <tr>
                            <td>${i + 1}</td>
                            <td>${row.nisn}</td>
                            <td>${row.nama_siswa}</td>
                            <td>${row.id_kelas}</td>
                            <td>${row.tahun_pelajaran}</td>
                            ${data.kegiatanList.map(kegiatan => {
                                var alias = kegiatan.replace(/\s+/g, '_').toLowerCase();
                                return `<td class="editable" data-nisn="${row.nisn}" data-field="${kegiatan}">${row[alias] ?? '-'}</td>`;
                            }).join('')}
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;

        $tableContainer.html(headerHtml + tableHtml);

        // Re-attach editable cell event listeners
        attachEditableListeners();
    }

    // Attach event listeners for editable cells
    function attachEditableListeners() {
        $('.editable').on('click', function () {
            var $cell = $(this);
            var nisn = $cell.data('nisn');
            var field = $cell.data('field');
            var currentValue = $cell.text() === '-' ? '' : $cell.text();

            // Replace cell content with input
            $cell.html(`<input type="text" value="${currentValue}" class="form-control form-control-sm">`);
            var $input = $cell.find('input');
            $input.focus();

            // Save on blur or enter
            $input.on('blur keypress', function (e) {
                if (e.type === 'blur' || e.which === 13) {
                    saveValue($cell, $input, nisn, field);
                }
            });
        });
    }

    // Initialize Bootstrap toast
    const toastElement = document.getElementById("notificationToast");
    const toast = new bootstrap.Toast(toastElement, {
        delay: 3000, // Auto-hide dalam 3 detik
    });

    // Make cells with class 'editable' clickable
    $("td.editable").on("click", function () {
        var $cell = $(this);
        // Prevent multiple inputs in the same cell
        if ($cell.find("input").length) return;

        // Ambil attribute isi sekarang, nisn, dan nilai apa yang mau di edit
        var currentValue =
            $cell.text().trim() === "-" ? "" : $cell.text().trim();
        var nisn = $cell.data("nisn");
        var field = $cell.data("field");

        $cell.html(
            `<input type="text" class="editable-input" value="${currentValue}" />`
        );

        // Focus on the input
        var $input = $cell.find("input");
        $input.focus();

        // Save on Enter key or blur
        $input
            .on("keypress", function (e) {
                if (e.which === 13) {
                    // Enter key
                    saveValue($cell, $input, nisn, field);
                }
            })
            .on("blur", function () {
                saveValue($cell, $input, nisn, field);
            });
    });

    function saveValue($cell, $input, nisn, field) {
        var newValue = $input.val().trim() || "-";
        $cell.text(newValue);
        $input.remove();

        // Ensure nisn is a string
        nisn = String(nisn);

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

    function showToast(message, bgClass) {
        // Update toast content and style
        $("#toastMessage").text(message);
        $("#notificationToast")
            .removeClass("text-bg-primary text-bg-success text-bg-danger")
            .addClass(bgClass);

        // Munculin
        toast.show();
    }
});
