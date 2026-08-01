document.addEventListener('DOMContentLoaded', function () {
    // Custom JS for Users page
    document.addEventListener('change', function (e) {
        const target = e.target;
        if (target.classList.contains('user-toggle-switch')) {
            const checkbox = target;
            const userId = checkbox.dataset.id;
            const isChecked = checkbox.checked ? 1 : 0;

            fetch('/menus/users/' + userId + '/status', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ is_active: isChecked })
            })
            .then(response => {
                if (!response.ok) throw new Error('Request failed');
                return response.json();
            })
            .then(data => {
                // Show notification if toastr exists
                if (typeof toastr !== 'undefined') {
                    toastr.success(data.is_active ? 'User activated successfully.' : 'User deactivated successfully.');
                }
            })
            .catch(() => {
                checkbox.checked = !checkbox.checked;
                if (typeof toastr !== 'undefined') {
                    toastr.error('Unable to update user status.');
                }
            });
        }
    });
});
