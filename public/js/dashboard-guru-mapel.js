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
        fetchFilteredData($firstTab.data("mapel"), $("#tahunFilter").val() || "", $("#kelasFilter").val() || "");
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
                // Use active tab's mapel
                const activeMapel = $("#mapelTabs .nav-link.active").data("mapel") || "";
                fetchFilteredData(activeMapel, $("#tahunFilter").val() || "", $("#kelasFilter").val() || "");
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
        fetchFilteredData(mapel, $("#tahunFilter").val() || "", $("#kelasFilter").val() || "");
    });

    // Handle kelas and tahun filter changes
    $("#tahunFilter, #kelasFilter").on("change", function () {
        const activeMapel = $("#mapelTabs .nav-link.active").data("mapel") || "";
        const tahun = $("#tahunFilter").val() || "";
        const kelas = $("#kelasFilter").val() || "";
        console.log("Filter change triggered:", {
            mapel: activeMapel,
            tahun_pelajaran: tahun,
            id_kelas: kelas,
        });
        fetchFilteredData(activeMapel, tahun, kelas);
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
                // Maintain kelas and tahun filter states
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
                            <td>${
                                row.semester === "Ganjil"
                                    ? `${row.tahun_pelajaran}-1`
                                    : row.semester === "Genap"
                                    ? `${row.tahun_pelajaran}-2`
                                    : row.tahun_pelajaran
                            }</td>
                            ${data.kegiatanList
                                .map((kegiatan) => {
                                    var alias = kegiatan
                                        .replace(/\s+/g, "_")
                                        .toLowerCase();
                                    return `<td class="editable"
                                            data-nisn="${row.nisn}"
                                            data-field="${kegiatan}"
                                            data-semester="${row.semester}"
                                            data-tahun_pelajaran="${row.tahun_pelajaran}"
                                            data-mapel="${row.nama_mapel}">${
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

    // Fungsi untuk membuat cell bisa diubah secara langsung
    function attachEditableListeners() {
        $(".editable").on("click", function () {
            var $cell = $(this);
            var nisn = $cell.data("nisn");
            var kegiatan = $cell.data("field");
            var semester = $cell.data("semester");
            var tahun_pelajaran = $cell.data("tahun_pelajaran");
            var mapel = $cell.data("mapel");
            var currentValue = $cell.text() === "-" ? "" : $cell.text();

            // validation
            if (!nisn || !kegiatan || !semester || !tahun_pelajaran || !mapel) {
                showToast("Data tidak lengkap!", "text-bg-danger");
                return;
            }

            $cell.html(
                `<input type="text" value="${currentValue}" class="form-control form-control-sm">`
            );
            var $input = $cell.find("input");
            $input.focus();

            $input.on("blur keypress", function (e) {
                if (e.type === "blur" || e.which === 13) {
                    saveValue($cell, $input, nisn, kegiatan, semester, tahun_pelajaran, mapel);
                }
            });
        });
    }

    // Save updated value
    function saveValue($cell, $input, nisn, kegiatan, semester, tahun_pelajaran, mapel) {
        var newValue = $input.val().trim() || "-";
        $cell.text(newValue);
        $input.remove();

        console.log("saveValue inputs:", {
            nisn: nisn,
            kegiatan: kegiatan,
            value: newValue,
            semester: semester,
            tahun_pelajaran: tahun_pelajaran,
            mapel: mapel,
        });

        showToast("Sedang menyimpan...", "text-bg-primary");

        $.ajax({
            url: "/dashboard/guru-mapel/update-nilai",
            type: "POST",
            data: {
                nisn: nisn,
                kegiatan: kegiatan,
                nilai: { [kegiatan]: newValue === "-" ? null : newValue }, // Send as array
                mapel: mapel,
                tahun_pelajaran: tahun_pelajaran,
                semester: semester,
                tanggal: new Date().toISOString().split("T")[0], // buang semua setelah "T" pada format date
            },
            success: function (response) {
                console.log("AJAX success:", response);
                showToast("Sukses Update Data!", "text-bg-success");
                // Use active tab's mapel
                const activeMapel = $("#mapelTabs .nav-link.active").data("mapel") || "";
                fetchFilteredData(activeMapel, $("#tahunFilter").val() || "", $("#kelasFilter").val() || "");
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
