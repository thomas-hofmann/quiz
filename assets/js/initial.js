$(function() {
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
    if ($('#codeInput').length) {
        $('#codeInput').on('change', function() {
            // Den Wert des Eingabefelds überprüfen
            if ($(this).val().trim() !== '') {
                // Wenn das Feld nicht leer ist, aktivieren Sie den Button
                $('#startQuizBtn').prop('disabled', false);
            } else {
                // Andernfalls deaktivieren Sie den Button
                $('#startQuizBtn').prop('disabled', true);
            }
        });
    }

    $('.quiz__final-initial').on('click', function(event) {
        let loadingSpan = $('#loading');
        loadingSpan.html('<i class="fa-solid fa-spinner fa-spin"></i>');
    });

    $('#startQuizBtn').on('click', function(event) {
        let loadingSpan = $('#loading');
        loadingSpan.html('<i class="fa-solid fa-spinner fa-spin"></i>');
    });
});