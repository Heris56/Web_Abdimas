$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

$(document).ready(function () {
    // Initialize with first mapel tab
    const $firstTab = $("#mapelTabs .nav-link").first();
    if ($firstTab.length) {
        $firstTab.addClass("active");
        fetchFilteredData($firstTab.data("mapel"), $("#tahunFilter").val() || "", $("#kelasFilter").val() || "", $("#semesterFilter").val() || "");
    }

    // Handle form submission for input nilai
    $('#inputNilaiForm').on('submit', function (e) {
        e.preventDefault();
        var nisn = $('#nisnSelect').val();
        var kegiatan = $('#kegiatanSelect').val();
        var nilai = $('#nilaiInput').val();
        var mapel = $('#mapelSelect').val();
        var tahun = $('#tahunSelect').val();
        var kelas = $('#kelasFilter').val() || '';
        var semester = $('#semesterFilter').val() || '';

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
                id_kelas: kelas,
                semester: semester
            },
            success: function (response) {
                console.log('Input nilai success:', response);
                showToast('Nilai berhasil disimpan!', 'text-bg-success');
                $('#inputNilaiModal').modal('hide');
                $('#inputNilaiForm')[0].reset();
                // Use active tab's mapel and current filter values
                const activeMapel = $("#mapelTabs .nav-link.active").data("mapel") || "";
                fetchFilteredData(activeMapel, $("#tahunFilter").val() || "", $("#kelasFilter").val() || "", $("#semesterFilter").val() || "");
            },
            error: function (xhr, status, error) {
                console.error('Input nilai error:', { status, error, responseText: xhr.responseText });
                let message = xhr.responseJSON?.message || 'Gagal menyimpan nilai!';
                showToast(message, 'text-bg-danger');
            }
        });
    });

    // Handle tab clicks
    $("#mapelTabs .nav-link").on("click", function () {
        const mapel = $(this).data("mapel");
        console.log("Tab clicked:", { mapel });
        fetchFilteredData(mapel, $("#tahunFilter").val() || "", $("#kelasFilter").val() || "", $("#semesterFilter").val() || "");
    });

    // Handle kelas, tahun, and semester filter changes
    $("#tahunFilter, #kelasFilter, #semesterFilter").on("change", function () {
        const activeMapel = $("#mapelTabs .nav-link.active").data("mapel") || "";
        const tahun = $("#tahunFilter").val() || "";
        const kelas = $("#kelasFilter").val() || "";
        const semester = $("#semesterFilter").val() || "";
        console.log("Filter change triggered:", {
            mapel: activeMapel,
            tahun_pelajaran: tahun,
            id_kelas: kelas,
            semester: semester
        });
        fetchFilteredData(activeMapel, tahun, kelas, semester);
    });

    function fetchFilteredData(mapel, tahun, kelas, semester) {
        console.log("Fetching data:", {
            mapel: mapel,
            tahun_pelajaran: tahun,
            id_kelas: kelas,
            semester: semester
        });
        $.ajax({
            url: "/dashboard/guru-mapel",
            type: "GET",
            data: {
                mapel: mapel,
                tahun_pelajaran: tahun,
                id_kelas: kelas,
                semester: semester
            },
            success: function (response) {
                console.log("AJAX success:", response);
                updateTable(response);
                $("#tahunFilter").val(tahun);
                $("#kelasFilter").val(kelas);
                $("#semesterFilter").val(semester);
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

        // Update table with new values
        var tableHtml = `
            <table class="table table-bordered table-sm" id="nilaiTable">
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
                            <td>${row.semester === 'Ganjil' ? row.tahun_pelajaran + '-1' : row.semester === 'Genap' ? row.tahun_pelajaran + '-2' : row.tahun_pelajaran}</td>
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

        $tableContainer.html(tableHtml);
        attachEditableListeners();
    }

    // Make cells editable
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
                semester: $("#semesterFilter").val() || ""
            },
            success: function (response) {
                console.log("AJAX success:", response);
                showToast("Sukses Update Data!", "text-bg-success");
                const activeMapel = $("#mapelTabs .nav-link.active").data("mapel") || "";
                fetchFilteredData(activeMapel, $("#tahunFilter").val() || "", $("#kelasFilter").val() || "", $("#semesterFilter").val() || "");
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

    // Show toast notification
    function showToast(message, className) {
        console.log("Toast:", message, className);
        const toastElement = document.getElementById("notificationToast");
        const toastBody = document.getElementById("toastMessage");

        // Update toast content
        toastBody.textContent = message;

        // Update toast color based on status
        toastElement.classList.remove(
            "text-bg-primary",
            "text-bg-success",
            "text-bg-danger"
        );
        toastElement.classList.add(className);

        // Auto-close toast after 3 seconds
        const toast = new bootstrap.Toast(toastElement, {
            delay: 3000,
        });
        toast.show();
    }

    attachEditableListeners();
});
