$(function() {
    $('#quiz-form').on('submit', function(event) {
        event.preventDefault(); // Verhindert das Standard-Formular-Absenden

        let form = $('#quiz-form');
        let url = form.attr('action');

        let loadingSpan = $('#loading');
        let originalContent = loadingSpan.html();

        loadingSpan.html('<i class="fa-solid fa-spinner fa-spin"></i>');

        $.ajax({
            type: form.attr('method'),
            url: url,
            data: form.serialize(), // Serialisiert die Formulardaten
            success: function(response) {
                // Ersetzt den HTML-Code mit der neuen Frage
                let newContent = $(response).find('.quiz-container').html();
                // Fade out the current content
                $('.quiz-container').fadeOut(200, function() {
                    // Replace the HTML content
                    $(this).html(newContent);
                    loadingSpan.html(originalContent);
                    // Fade in the new content
                    $(this).fadeIn(200);
                });
            },
            error: function(status, error) {
                console.error('AJAX Error:', status, error);
                loadingSpan.html(originalContent);
            }
        });
    });

    $('#newName').on('click', function(event) {
        event.preventDefault();

        let link = $('#newName');
        let url = link.attr('href');

        $.ajax({
            type: 'get',
            url: url,
            success: function(response) {
                // Ersetzt den HTML-Code mit der neuen Frage
                // Fade out the current content
                $('#matrikelnummerChange').fadeOut(150, function() {
                    // Replace the HTML content
                    $(this).html(response.matrikelnummer);
                    // Fade in the new content
                    $(this).fadeIn(150);
                });
            },
            error: function(status, error) {
                console.error('AJAX Error:', status, error);
            }
        });
    });

    $('.answer__radio').on('click', function() {
        // Enable the submit button
        $('#submit-btn').prop('disabled', false);
    });
});