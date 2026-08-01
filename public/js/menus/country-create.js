document.addEventListener('DOMContentLoaded', function () {
    var form = document.querySelector('.role-form-inner');
    if (!form) {
        return;
    }

    form.addEventListener('submit', function (event) {
        // ensure rich text editors save content to textareas
        if (window.tinymce) {
            try { tinymce.triggerSave(); } catch (e) { /* noop */ }
        }

        // TODO: implement real submission logic (AJAX or normal POST)
        // For now keep default submit (remove the next line to allow actual submission)
        event.preventDefault();
        alert('Country create form submitted (testing).');
    });
});
