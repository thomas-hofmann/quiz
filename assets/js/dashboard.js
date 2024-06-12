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
});