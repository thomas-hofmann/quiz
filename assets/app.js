const $ = require('jquery');
import AOS from 'aos';
import 'aos/dist/aos.css';

$(window).on('load', function() {
    $('.container--fade-in').fadeIn(300);
    AOS.init({
        once: true,
        duration: 500,
        easing: "ease-out-cubic",
    });
});

require('bootstrap');

import './styles/app.scss';
import './js/base.js';
import './js/initial.js';
import './js/leaderboard.js';
import './js/dashboard.js';
import './js/chart.js';
import './js/index.js';
