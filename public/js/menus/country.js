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
            var query = this.value.trim().toLowerCase();

            rows.forEach(function (row) {
                var text = row.textContent.toLowerCase();
                row.style.display = text.indexOf(query) !== -1 ? '' : 'none';
            });
        });
    });
});