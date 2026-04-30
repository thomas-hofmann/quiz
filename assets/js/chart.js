import Chart from 'chart.js/auto';  // Dies importiert Chart.js korrekt

$(function() {
    const quizId = $('#chart-row').data('quiz');  // Hole die Quiz-ID

    if (!quizId) {
        return;
    }

    const barChart = Chart.getChart('barChart');  // Hole das bestehende Diagramm für das Balkendiagramm

    if (!barChart) {
        console.error('Das Diagramm konnte nicht gefunden werden.');
        return;
    }

    // Funktion, um die Diagrammdaten zu aktualisieren
    function updateCharts() {
        $.ajax({
            url: `/chart-data/${quizId}`,
            method: 'GET',
            success: function(response) {
                if (!response.labels || !response.datasets || !response.datasets[0].data) {
                    console.error('Ungültige oder fehlende Daten:', response);
                    return;
                }

                // Berechne den maximalen Wert der Y-Achse basierend auf den aktuellen Daten
                const maxScore = Math.max(...response.datasets[0].data);

                // Aktualisiere das Balkendiagramm mit den neuen Daten
                barChart.data.labels = response.labels;  // Labels aktualisieren
                barChart.data.datasets[0].data = response.datasets[0].data;  // Daten aktualisieren
                barChart.options.scales.y.suggestedMax = maxScore;  // Setze den neuen suggestedMax
                barChart.update();  // Balkendiagramm aktualisieren
            },
            error: function(xhr) {
                console.error('Fehler beim Abrufen der Daten:', xhr.responseJSON || xhr.responseText);
            }
        });
    }

    // Diagramme alle 2 Sekunden aktualisieren
    setInterval(updateCharts, 2000);
});
