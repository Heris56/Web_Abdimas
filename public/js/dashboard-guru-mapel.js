// Set header supaya ga perlu manggil _token lagi untuk AJAX operation
$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

$(document).ready(function () {
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
