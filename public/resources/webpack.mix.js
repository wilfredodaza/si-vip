let mix = require('laravel-mix');

mix.js('assets/js/app.js', 'dist/js/').vue({ extractStyles: true });
mix.js('assets/js/email.js', 'dist/js/');