let mix = require('laravel-mix');
require('vuetifyjs-mix-extension');

mix.setPublicPath('public/vendor/lighthouse-dashboard')
    .setResourceRoot('/vendor/lighthouse-dashboard')
    .js('resources/js/app.js', 'js/')
    .sass('resources/css/app.scss', 'css/')                                 
    .webpackConfig({
        output: {
            publicPath: '/vendor/lighthouse-dashboard/',
            chunkFilename: 'js/[name].js?id=[chunkhash]'
        }       
    })
    .vue({ version: 2 })
    .vuetify()
    .sourceMaps()
    .version()
