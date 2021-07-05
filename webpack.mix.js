const mix = require('laravel-mix');

mix
  .disableNotifications()
  .options({
    processCssUrls: false
  })
  .js('resources/js/app.js', 'assets/js/app.js').vue()
  .sass('resources/sass/cart.scss', 'assets/css/cart.css')
;
