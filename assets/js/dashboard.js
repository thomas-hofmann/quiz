import 'jquery-ui/ui/widgets/sortable';
import 'jquery-ui/ui/disable-selection';

$(function() {
    if ($("#sortable").length) {
        // Aktivieren der Sortierbarkeit mit jQuery UI
        $("#sortable").sortable();
        $("#sortable").disableSelection();

        // Klick-Event für den Speichern-Button
        $('#save-order').on('click', function(event) {
            // Positionen der Fragen erfassen
            let positions = {};
            $('#sortable li').each(function(index) {
                positions[$(this).data('id')] = index + 1;
            });

            // AJAX-Anfrage zum Speichern der neuen Reihenfolge
            $.ajax({
                url: '/question-sort',
                method: 'POST',
                data: { positions: positions },
                success: function(response) {
                    alert(response);
                },
                error: function(xhr, status, error) {
                    alert('Fehler beim Speichern der Sortierung: ' + error);
                }
            });
        });
    }
    if ($(".toggle-quiz").length) {
        $('.toggle-quiz').on('change', function() {
            const url = $(this).data('url');
            const isEnabled = $(this).is(':checked');
            const label = $(this).next('label');
        
            $.ajax({
                url: url,
                type: 'POST',
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token("toggle_quiz") }}'
                },
                data: JSON.stringify({ isEnabled: isEnabled }),
                success: function(data) {
                    if (data.success) {
                        // Ändere den Text des Labels basierend auf dem isEnabled-Wert
                        if (isEnabled) {
                            label.text('Quiz ist aktiviert');
                        } else {
                            label.text('Quiz ist deaktiviert');
                        }
                    } else {
                        alert('Fehler beim Aktualisieren des Quiz-Status.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Fehler:', error);
                }
            });
        });
    }
    if ($("#show-answers-button").length) {
        $('#show-answers-button').on('click', function(event) {
            event.preventDefault();  // Verhindert das Standardverhalten des Links
            var url = $(this).attr('data-href');  // Holt die URL aus dem href-Attribut des Links
            $('#show-answers-button').html('<i class="fa-solid fa-spinner fa-spin"></i></i> Ergebnisse aktualisieren');
            $.ajax({
                url: url,
                method: 'GET',
                success: function(data) {
                    const newContent = $(data).find('#answer-stats-container').html();
                    $('#answer-stats-container').html(newContent);
                    $('#show-answers-button .fa-solid').fadeOut(300, function() {
                        $('#show-answers-button').html('<i class="fa-solid fa-arrows-rotate"></i> Ergebnisse aktualisieren');
                        $(this).fadeIn(300);
                    });
                    
                }
            });
        });
    }
    if ($('.code__bool').length) {
        const $codeBoolSelf = $('#codeBoolSelf');
        const $quizcode = $('#quizcode');
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
    }
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
});