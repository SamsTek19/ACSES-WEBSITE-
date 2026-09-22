// ACSES AJAX & Alert Helper
// Requires jQuery and SweetAlert2

// Theme colors (these will be set dynamically from PHP)
const ACSES_THEME = {
    primary: window.ACSES_PRIMARY_COLOR || '#0056b3',
    secondary: window.ACSES_SECONDARY_COLOR || '#00b386',
    white: window.ACSES_WHITE_COLOR || '#fff'
};

// SweetAlert2 default config
const swalWithTheme = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-primary',
        cancelButton: 'btn btn-secondary'
    },
    buttonsStyling: false,
    background: ACSES_THEME.white,
    color: ACSES_THEME.primary,
    confirmButtonColor: ACSES_THEME.primary,
    cancelButtonColor: ACSES_THEME.secondary
});

function acsesAlert(type, title, text) {
    swalWithTheme.fire({
        icon: type,
        title: title,
        text: text
    });
}

function acsesConfirm(title, text, confirmCallback) {
    swalWithTheme.fire({
        title: title,
        text: text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed && typeof confirmCallback === 'function') {
            confirmCallback();
        }
    });
}

// AJAX form submit helper
function acsesAjaxForm($form, onSuccess, onError) {
    $form.on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        $.ajax({
            url: $form.attr('action'),
            type: $form.attr('method') || 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    acsesAlert('success', 'Success', response.message || 'Operation successful!');
                    if (onSuccess) onSuccess(response);
                } else {
                    acsesAlert('error', 'Error', response.message || 'Something went wrong.');
                    if (onError) onError(response);
                }
            },
            error: function(xhr) {
                acsesAlert('error', 'Error', xhr.responseJSON?.message || 'Server error.');
                if (onError) onError(xhr);
            }
        });
    });
} 