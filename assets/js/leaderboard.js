$(function() {
    function updateLeaderboard() {
        const quizId = $('.leaderboard').data('quiz');
        $.ajax({
            url: `/quiz-leaderboard/${quizId}`,
            method: 'GET',
            success: function(data) {
                const newTable = $(data).find('#leaderboard-body').html();
                $('#leaderboard-body').html(newTable);
            },
            error: function() {
                console.error('Error updating leaderboard');
            }
        });
    }
    if ($('.leaderboard').length) {
        setInterval(updateLeaderboard, 2000);
    }
});