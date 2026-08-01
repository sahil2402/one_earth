document.addEventListener('DOMContentLoaded', function () {
    var toggle = document.getElementById('toggle-role-form');
    var formContainer = document.getElementById('role-form-container');

    var formCard = document.getElementById('role-form-card');

    if (toggle && formCard) {
        toggle.addEventListener('click', function () {
            formCard.classList.toggle('collapsed');
            toggle.textContent = formCard.classList.contains('collapsed') ? 'Add new role' : 'Hide form';
        });
    }

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

    function sortTable(table, columnIndex, isDate) {
        var tbody = table.tBodies[0];
        var rows = Array.from(tbody.rows);
        var currentDirection = table.getAttribute('data-sort-direction') || 'asc';
        var nextDirection = currentDirection === 'asc' ? 'desc' : 'asc';

        rows.sort(function (a, b) {
            var aText = a.cells[columnIndex].textContent.trim();
            var bText = b.cells[columnIndex].textContent.trim();

            if (isDate) {
                aText = new Date(aText);
                bText = new Date(bText);
            } else if (columnIndex === 1) {
                aText = aText.toLowerCase().includes('active') ? 0 : 1;
                bText = bText.toLowerCase().includes('active') ? 0 : 1;
            } else {
                aText = aText.toLowerCase();
                bText = bText.toLowerCase();
            }

            if (aText < bText) return nextDirection === 'asc' ? -1 : 1;
            if (aText > bText) return nextDirection === 'asc' ? 1 : -1;
            return 0;
        });

        rows.forEach(function (row) {
            tbody.appendChild(row);
        });

        table.setAttribute('data-sort-direction', nextDirection);
    }

    document.querySelectorAll('.sort-header').forEach(function (button) {
        var table = document.querySelector('table#roles-table');
        button.addEventListener('click', function () {
            var columnIndex = parseInt(button.getAttribute('data-sort-column'), 10);
            var isDate = button.getAttribute('data-sort-type') === 'date';
            if (table) {
                sortTable(table, columnIndex, isDate);
            }
        });
    });

    function showToast(message, type) {
        if (typeof toastr !== 'undefined') {
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: 'toast-top-right',
                timeOut: 3000,
                extendedTimeOut: 1000,
            };

            if (type === 'error') {
                toastr.error(message);
            } else {
                toastr.success(message);
            }
            return;
        }

        var container = document.getElementById('toast-container');
        if (!container) {
            return;
        }

        var toast = document.createElement('div');
        toast.className = 'toast-message ' + (type || 'success');
        toast.textContent = message;

        container.appendChild(toast);

        window.setTimeout(function () {
            toast.classList.add('toast-hide');
            toast.addEventListener('transitionend', function () {
                toast.remove();
            });
        }, 2800);
    }

    document.addEventListener('change', function (e) {

        const target = e.target;

        // MENU toggle (checkbox with class menu-toggle-switch)
        if (target.classList.contains('menu-toggle-switch')) {
            const checkbox = target;
            const menuId = checkbox.dataset.id;
            const newValue = checkbox.checked ? 1 : 0;

            fetch('/settings/menu/' + menuId + '/status', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ is_active: newValue })
            })
            .then(response => {
                if (!response.ok) throw new Error('Request failed');
                return response.json();
            })
            .then(data => {
                showToast(data.is_active ? 'Menu activated successfully.' : 'Menu deactivated successfully.', 'success');
            })
            .catch(() => {
                checkbox.checked = !checkbox.checked;
                showToast('Unable to update menu status.', 'error');
            });

            return;
        }

        // ROLE toggle (checkbox with class status-switch)
        if (target.classList.contains('status-switch')) {
            const checkbox = target;
            const url = checkbox.dataset.url;
            const newValue = checkbox.checked ? 1 : 0;

            fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ is_active: newValue })
            })
            .then(response => {
                if (!response.ok) throw new Error('Request failed');
                return response.json();
            })
            .then(data => {
                // update adjacent status label if present
                const row = checkbox.closest('tr');
                if (row) {
                    const label = row.querySelector('.status-label');
                    if (label) {
                        label.textContent = data.is_active ? 'Active' : 'Inactive';
                        label.classList.toggle('active', !!data.is_active);
                        label.classList.toggle('inactive', !data.is_active);
                    }
                }
                showToast(data.is_active ? 'Role activated successfully.' : 'Role deactivated successfully.', 'success');
            })
            .catch(() => {
                checkbox.checked = !checkbox.checked;
                showToast('Unable to update role status.', 'error');
            });
        }

    });

    // Owner dropdown behavior: toggle and close on outside click / Escape
    document.querySelectorAll('.owner-menu').forEach(function (menu) {
        var btn = menu.querySelector('.owner-btn');
        var dropdown = menu.querySelector('.owner-dropdown');
        if (!btn || !dropdown) return;

        btn.addEventListener('click', function (ev) {
            ev.stopPropagation();
            var isOpen = menu.classList.toggle('open');
            btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            dropdown.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        });
    });

    document.addEventListener('click', function () {
        document.querySelectorAll('.owner-menu.open').forEach(function (menu) {
            var btn = menu.querySelector('.owner-btn');
            var dropdown = menu.querySelector('.owner-dropdown');
            menu.classList.remove('open');
            if (btn) btn.setAttribute('aria-expanded', 'false');
            if (dropdown) dropdown.setAttribute('aria-hidden', 'true');
        });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.owner-menu.open').forEach(function (menu) {
                var btn = menu.querySelector('.owner-btn');
                var dropdown = menu.querySelector('.owner-dropdown');
                menu.classList.remove('open');
                if (btn) btn.setAttribute('aria-expanded', 'false');
                if (dropdown) dropdown.setAttribute('aria-hidden', 'true');
            });
        }
    });
});
