const mix = require('laravel-mix');

mix
  .options({
    processCssUrls: false
  })
  .js('resources/js/app.js', 'assets/js/app.js')
  .sass('resources/sass/cart.scss', 'assets/css/cart.css')
;
