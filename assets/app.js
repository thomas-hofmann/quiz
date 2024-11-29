const $ = require('jquery');

$(window).on('load', function() {
    $('.container--fade-in').fadeIn(300);
});

require('bootstrap');

import './styles/app.scss';
import './js/base.js';
import './js/initial.js';
import './js/leaderboard.js';
import './js/dashboard.js';
