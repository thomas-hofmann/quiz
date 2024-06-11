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
});