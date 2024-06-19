$(function() {
    function updateLeaderboard() {
        const quizId = $('.leaderboard').data('quiz');
        $.ajax({
            url: `/quiz-leaderboard/${quizId}`,
            method: 'GET',
            success: function(data) {
                const newContent = $(data).find('#container-stats').html();
                $('#container-stats').html(newContent);
            },
            error: function() {
                console.error('Error updating leaderboard');
            }
        });
    }
    if ($('.leaderboard').length) {
        setInterval(updateLeaderboard, 2000);
    }

    $('#show-answers-button').on('click', function(event) {
        event.preventDefault();  // Verhindert das Standardverhalten des Links
        var url = $(this).attr('data-href');  // Holt die URL aus dem href-Attribut des Links

        $.ajax({
            url: url,
            method: 'GET',
            success: function(data) {
                const newContent = $(data).find('#answer-stats-container').html();
                $('#answer-stats-container').html(newContent);
                $('#show-answers-button').html('<i class="fa-solid fa-arrows-rotate"></i> Aktualisieren');
            }
        });
    });
});