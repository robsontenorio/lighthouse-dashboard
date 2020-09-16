let mix = require('laravel-mix');

mix.setPublicPath('public/vendor/lighthouse-dashboard')
    .js('resources/js/app.js', 'js/')
    .sass('resources/css/app.scss', 'css/')
    .browserSync('localhost:8080')   // change this while developing in different host/port
    .webpackConfig({
        output: {
            publicPath: '/vendor/lighthouse-dashboard/',
            chunkFilename: 'js/[name].js?id=[chunkhash]'
        },
        resolve: {
            alias: {
                'vue$': 'vue/dist/vue.runtime.esm.js',
                '@': path.resolve('resources/js'),
            },
        },
    })
    .sourceMaps()
    .version()
