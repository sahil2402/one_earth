// Scripts for state menu page
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.datatable-toolbar').forEach(function (toolbar) {
        var tableId = toolbar.dataset.tableId;
        var table = document.getElementById(tableId);
        var searchInput = document.getElementById(tableId + '-search');

        if (!table || !searchInput) {
            return;
        }

        var rows = Array.from(table.tBodies[0]?.rows || []);

        searchInput.addEventListener('input', function () {
            var filter = searchInput.value.toLowerCase().trim();

            rows.forEach(function (row) {
                if (row.cells.length === 1 && row.cells[0].classList.contains('empty')) {
                    return;
                }
                var text = row.textContent.toLowerCase();
                row.style.display = text.indexOf(filter) > -1 ? '' : 'none';
            });
        });
    });
});
