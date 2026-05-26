/**
 * Main JavaScript File
 * SPP Application (Aplikasi SPP)
 * 
 * Provides layout behavior such as sidebar toggle on mobile devices.
 */
$(document).ready(function () {
    // Toggle Sidebar on mobile layout
    $("#sidebar-toggle").on("click", function (e) {
        e.stopPropagation();
        $("body").toggleClass("sidebar-open");
    });

    // Close Sidebar when clicking outside on mobile layout
    $(document).on("click", function (e) {
        if ($("body").hasClass("sidebar-open")) {
            if (!$(e.target).closest(".sidebar").length && !$(e.target).closest("#sidebar-toggle").length) {
                $("body").removeClass("sidebar-open");
            }
        }
    });

    // Initialize jQuery DataTables on tables marked with .datatable class
    if ($.fn.DataTable) {
        $('.datatable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json',
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Lanjut",
                    previous: "Sebelum"
                }
            }
        });
    }
});
