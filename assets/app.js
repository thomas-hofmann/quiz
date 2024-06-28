const $ = require('jquery');

require('bootstrap');
import './styles/app.scss';
import './js/initial.js';
import './js/leaderboard.js';
import './js/dashboard.js';

$(window).on('load', function() {
    $('.container--fade-in').fadeIn(300);
});
