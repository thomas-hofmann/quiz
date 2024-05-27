$(document).ready(function() {
    $('#quiz-form').on('submit', function(event) {
        event.preventDefault(); // Verhindert das Standard-Formular-Absenden

        let form = $('#quiz-form');
        let url = form.attr('action');

        $.ajax({
            type: form.attr('method'),
            url: url,
            data: form.serialize(), // Serialisiert die Formulardaten
            success: function(response) {
                // Ersetzt den HTML-Code mit der neuen Frage
                $('.quiz-container').html($(response).find('.quiz-container').html());
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
            }
        });
    });
});