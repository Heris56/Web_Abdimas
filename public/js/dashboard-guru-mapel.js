$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

let selectedMapelId = null;
function getActiveMapelId() {
    const activeTab = document.querySelector(".nav-link.active");
    return activeTab ? activeTab.getAttribute("data-id-mapel") : null;
}

document.addEventListener("DOMContentLoaded", function () {
    const kelasFilter = document.getElementById("kelasFilter");

    function populateKelasOptions(kelasArray) {
        kelasFilter.innerHTML = ""; // Clear current options
        if (!kelasArray || kelasArray.length === 0) {
            kelasFilter.innerHTML = '<option value="">Tidak ada kelas</option>';
            return;
        }
        kelasArray.forEach((kelas) => {
            const option = document.createElement("option");
            option.value = kelas;
            option.textContent = kelas;
            kelasFilter.appendChild(option);
        });
    }

    // Listen for tab clicks
    document.querySelectorAll("[data-id-mapel]").forEach((button) => {
        button.addEventListener("click", function () {
            selectedMapelId = this.getAttribute("data-id-mapel"); // Save globally

            const kelasList = mapelKelasMap[selectedMapelId] || [];
            populateKelasOptions(kelasList);

            updateTable(); // ← if this exists
        });
    });

    // Auto-fill for the first tab on page load
    const firstMapelId = document
        .querySelector("[data-id-mapel]")
        ?.getAttribute("data-id-mapel");
    if (firstMapelId && mapelKelasMap[firstMapelId]) {
        populateKelasOptions(mapelKelasMap[firstMapelId]);
    }
});

$(document).ready(function () {
    // Initialize with first mapel tab
    const $firstTab = $("#mapelTabs .nav-link").first();
    if ($firstTab.length) {
        $firstTab.addClass("active");
        fetchFilteredData(
            $firstTab.data("mapel"),
            $("#tahunFilter").val() || "",
            $("#kelasFilter").val() || "",
            $("#semesterFilter").val() || ""
        );
    }

    // Handle tab clicks
    $("#mapelTabs .nav-link").on("click", function () {
        const mapel = $(this).data("mapel");
        console.log("Tab clicked:", { mapel });
        fetchFilteredData(
            mapel,
            $("#tahunFilter").val() || "",
            $("#kelasFilter").val() || "",
            $("#semesterFilter").val() || ""
        );
    });

    // Handle kelas, tahun, and semester filter changes
    $("#tahunFilter, #kelasFilter, #semesterFilter").on("change", function () {
        const activeMapel =
            $("#mapelTabs .nav-link.active").data("mapel") || "";
        const tahun = $("#tahunFilter").val() || "";
        const kelas = $("#kelasFilter").val() || "";
        const semester = $("#semesterFilter").val() || "";
        console.log("Filter change triggered:", {
            mapel: activeMapel,
            tahun_pelajaran: tahun,
            id_kelas: kelas,
            semester: semester,
        });
        fetchFilteredData(activeMapel, tahun, kelas, semester);
    });

    function fetchFilteredData(mapel, tahun, kelas, semester) {
        console.log("Fetching data:", {
            mapel: mapel,
            tahun_pelajaran: tahun,
            id_kelas: kelas,
            semester: semester,
        });
        $.ajax({
            url: "/dashboard/guru-mapel",
            type: "GET",
            data: {
                mapel: mapel,
                tahun_pelajaran: tahun,
                id_kelas: kelas,
                semester: semester,
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
            <table class="table table-bordered table-sm" id="table-data">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Tahun Ajaran</th>
                        ${data.kegiatanList
                            .map(
                                (kegiatan) => `
      <th class="kegiatan-header">
        <div class="kegiatan-cell">
          ${kegiatan}
          <button class="delete-btn" data-kegiatan="${kegiatan}" data-id-mapel="${selectedMapelId}"><i class="bi bi-trash-fill"></i></button>
        </div>
      </th>
        `
                            )
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
                                    ? row.tahun_pelajaran + "-1"
                                    : row.semester === "Genap"
                                    ? row.tahun_pelajaran + "-2"
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
                                                data-tahun_pelajaran="${
                                                    row.tahun_pelajaran
                                                }"
                                                data-semester="${row.semester}"
                                                data-id_mapel="${
                                                    row.id_mapel ?? ""
                                                }"
                                                data-nip="${
                                                    row.nip_guru_mapel ?? ""
                                                }">
                                                ${row[alias] ?? "-"}
                                            </td>`;
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

    function attachEditableListeners() {
        $(".editable").on("click", function () {
            var $cell = $(this);
            var nisn = $cell.data("nisn");
            var field = $cell.data("field");
            var tahun_pelajaran = $cell.data("tahun_pelajaran") || "";
            var semester = $cell.data("semester") || "";
            var id_mapel = $cell.data("id_mapel") || "";
            var nip = $cell.data("nip") || "";
            var currentValue =
                $cell.text().trim() === "-" ? "" : $cell.text().trim();

            console.log("All cell data:", $cell.data());

            $cell.html(
                `<input type="text" value="${currentValue}" class="form-control form-control-sm">`
            );
            var $input = $cell.find("input");
            $input.focus();
            $input[0].setSelectionRange(
                $input.val().length,
                $input.val().length
            );

            $input.on("blur keypress", function (e) {
                if (e.type === "blur" || e.which === 13) {
                    saveValue(
                        $cell,
                        $input,
                        nisn,
                        field,
                        tahun_pelajaran,
                        semester,
                        id_mapel,
                        nip
                    );
                }
            });
        });
    }

    function saveValue(
        $cell,
        $input,
        nisn,
        field,
        tahun_pelajaran,
        semester,
        id_mapel,
        nip_guru_mapel
    ) {
        var newValue = $input.val().trim() || "-";
        $cell.text(newValue);
        $input.remove();

        // Log inputs for debugging
        console.log("saveValue inputs:", {
            nisn: nisn,
            field: field,
            value: newValue,
            tahun_pelajaran: tahun_pelajaran,
            semester: semester,
            id_mapel: id_mapel,
            nip_guru_mapel: nip_guru_mapel,
            nisn_type: typeof nisn,
            field_type: typeof field,
        });

        // Validate required fields
        if (!tahun_pelajaran || !semester || !id_mapel || !nip_guru_mapel) {
            showToast(
                "Data tidak lengkap (tahun_pelajaran, semester, id_mapel, atau nip kosong)!",
                "text-bg-danger"
            );
            return;
        }

        showToast("Sedang menyimpan...", "text-bg-primary");

        $.ajax({
            url: "/dashboard/guru-mapel/update-nilai",
            type: "POST",
            data: {
                nisn: nisn,
                field: field,
                value: newValue === "-" ? null : newValue,
                semester: $("#semesterFilter").val() || "",
                tahun_pelajaran: tahun_pelajaran,
                semester: semester,
                id_mapel: id_mapel,
                nip_guru_mapel: nip_guru_mapel,
            },
            success: function (response) {
                console.log("AJAX success:", response);
                showToast("Sukses Update Data!", "text-bg-success");
                const activeMapel =
                    $("#mapelTabs .nav-link.active").data("mapel") || "";
                fetchFilteredData(
                    activeMapel,
                    $("#tahunFilter").val() || "",
                    $("#kelasFilter").val() || "",
                    $("#semesterFilter").val() || ""
                );
            },
            error: function (xhr, status, error) {
                console.error("AJAX error:", {
                    status,
                    error,
                    responseText: xhr.responseText,
                });
                console.log("Data Used:", {
                    nisn: nisn,
                    field: field,
                    value: newValue,
                    tahun_pelajaran: tahun_pelajaran,
                    semester: semester,
                    id_mapel: id_mapel,
                    nip_guru_mapel: nip_guru_mapel,
                    nisn_type: typeof nisn,
                    field_type: typeof field,
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

document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById("cariSiswa");
    const tableContainer = document.getElementById("tableContainer");

    if (!input || !tableContainer) return;

    input.addEventListener("keyup", function () {
        const inputSearch = this.value.toLowerCase();
        const rows = tableContainer.querySelectorAll("#table-data tbody tr");

        rows.forEach((row) => {
            const namaSiswaCell = row.querySelector("td:nth-child(3)"); // Nama Siswa is the 3rd column
            if (namaSiswaCell) {
                const namaSiswa = namaSiswaCell.textContent.toLowerCase();
                row.style.display = namaSiswa.includes(inputSearch)
                    ? ""
                    : "none";
            } else {
                row.style.display = "none";
            }
        });
    });
});

document.addEventListener("click", function (e) {
    if (e.target.closest(".delete-btn")) {
        const btn = e.target.closest(".delete-btn");
        const kegiatan = btn.getAttribute("data-kegiatan");
        const id_mapel = getActiveMapelId();
        console.log("Active Mapel ID:", id_mapel);

        fetch("/dashboard/guru-mapel/delete-kegiatan", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
            body: JSON.stringify({ kegiatan, id_mapel: id_mapel }),
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.success) {
                    alert("Data berhasil dihapus");
                    location.reload();
                } else {
                    alert(
                        "Gagal menghapus: " +
                            (data.message || "Tidak diketahui")
                    );
                }
            })
            .catch((err) => {
                console.error("Error deleting:", err);
                alert("Terjadi kesalahan pada server.");
            });
    }
});
