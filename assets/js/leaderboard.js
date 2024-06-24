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
});