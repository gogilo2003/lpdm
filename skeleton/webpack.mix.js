const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */
const { exec } = require('child_process');
mix.setPublicPath('public')
mix.vue()
    .js('resources/js/{{ packageName }}.js', 'js')
    .sass('resources/scss/{{ packageName }}.scss', 'public/css')
    .postCss('resources/css/{{ packageName }}.css', 'public/css', [
        //
    ]).after(() => {
        // exec('php ../../../artisan vendor:publish --force --tag={{ packageName }}.public', (res, stdout, stderr) => { console.log(stdout); });
    });
