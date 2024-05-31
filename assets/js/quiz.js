$(function() {
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
                let newContent = $(response).find('.quiz-container').html();
                // Fade out the current content
                $('.quiz-container').fadeOut(250, function() {
                    // Replace the HTML content
                    $(this).html(newContent);
                    // Fade in the new content
                    $(this).fadeIn(250);
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

    const $codeBoolSelf = $('#codeBoolSelf');
    const $quizcode = $('#quizcode');

    // Add an event listener to the radio button
    $('.code__bool').on('change', function() {
        // Check if the radio button is checked
        if ($codeBoolSelf.is(':checked')) {
            // Enable the input field
            $quizcode.prop('disabled', false);
            $quizcode.prop('required', true);
        } else {
            // Disable the input field (in case of multiple radio buttons affecting this field)
            $quizcode.prop('disabled', true);
            $quizcode.prop('required', false);
        }
    });
});