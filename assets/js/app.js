require('../scss/app.scss');

const $ = require('jquery');
global.$ = global.jQuery = $;

const bootstrap = require('bootstrap');
require('bootstrap/js/dist/modal');
require('bootstrap/js/dist/tooltip');

const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
