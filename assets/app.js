const $ = require('jquery');

require('bootstrap');
import './styles/app.scss';
import './js/initial.js';
import './js/leaderboard.js';
import './js/dashboard.js';

$(function() {
    if ($('.container--fade-in').length) {
        $('.container--fade-in').fadeIn(300);
    }
});
