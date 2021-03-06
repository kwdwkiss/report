let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix
    .webpackConfig({
        module: {
            rules: [
                {
                    test: require.resolve('jquery'),
                    use: [
                        {
                            loader: 'expose-loader',
                            options: 'jQuery'
                        },
                        {
                            loader: 'expose-loader',
                            options: '$'
                        },
                    ],
                }
            ]
        }
    });
//.js('Modules/Admin/Resources/assets/js/app.js', 'public/js/admin/')
//.js('Modules/Index/Resources/assets/js/app.js', 'public/js/index/')
mix.js('resources/assets/js/admin/app.js', 'public/js/admin/')
mix.js('resources/assets/js/agent/app.js', 'public/js/agent/')
mix.js('resources/assets/js/index/app.js', 'public/js/index/')
// .extract(['element-ui', 'element-ui/lib/theme-chalk/index.css'], 'js/element')
// .extract(['lodash', 'jquery', 'bootstrap-sass', 'axios', 'vue', 'vue-router'], 'js/vendor.js')

mix.sass('resources/assets/sass/app.scss', 'public/css')

if (mix.inProduction()) {
    mix.version()
}

