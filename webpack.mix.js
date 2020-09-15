let mix = require('laravel-mix');

mix.setPublicPath('public/vendor/lighthouse-dashboard')
    .js('resources/js/app.js', 'js/')
    .sass('resources/css/app.scss', 'css/')
    .browserSync('localhost:8080')
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
    .babelConfig({
        plugins: ['@babel/plugin-syntax-dynamic-import']
    })
    .sourceMaps()

// browserSync not working without version?
// if (mix.inProduction()) {
mix.version()
// }

