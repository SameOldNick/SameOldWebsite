const output = require('dotenv').config({ debug: true });

if (output.error !== undefined) {
    console.error('Unable to parse .env file.');
    console.error(output.error);

    process.exit(1);
}

const path = require('path');
const { DateTime } = require('luxon');
const { buildUrl } = require('build-url-ts');
const mix = require('laravel-mix');
const webpack = require('webpack');
const CopyPlugin = require('copy-webpack-plugin');
const TsconfigPathsPlugin = require('tsconfig-paths-webpack-plugin');

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

mix.setPublicPath('public');

mix.webpackConfig({
    resolve: {
        plugins: [
            new TsconfigPathsPlugin()
        ]
    },
    plugins: [
        new webpack.DefinePlugin({
            __BUILD_DATE__: JSON.stringify(DateTime.now().toISO()),
            __WEBPACK_VERSION__: JSON.stringify(webpack.version),
            __ENV__: JSON.stringify(process.env.APP_ENV),
            __DEBUG__: JSON.stringify(process.env.APP_DEBUG),
            __NAME__: JSON.stringify(process.env.APP_NAME),
            __URL__: JSON.stringify(process.env.APP_URL),
            __API_URL__: JSON.stringify(buildUrl(process.env.APP_URL, { path: 'api' })),
		}),
        new webpack.ProvidePlugin({
            $: 'jquery',
            jQuery: 'jquery',
            'window.jQuery': 'jquery',
            bootstrap: 'bootstrap',
            'window.bootstrap': 'bootstrap'
        }),
        new CopyPlugin({
            patterns: [
                {
                    from: path.resolve(__dirname, "resources/images"),
                    to: path.resolve(__dirname, "public/images")
                },
                {
                    context: './node_modules/tinymce/',
                    from: '**/*.(min.js|min.css|woff)',
                    to: './js/tinymce/[path][name][ext]'
                }
            ]
        }),
    ]
});

mix.ts('resources/ts/main/index.ts', 'public/js/main.js')
   .ts('resources/ts/admin/index.tsx', 'public/js/admin.js')
   .extract(['react', 'react-dom'], 'public/js/admin-vendor.js')
   .extract(['jquery', 'bootstrap'], 'public/js/main-vendor.js')
   .sass('resources/scss/main/all.scss', 'public/css/main.css')
   .sass('resources/scss/admin/all.scss', 'public/css/admin.css');

if (mix.inProduction()) {
    mix.version();
} else {
    mix.sourceMaps();
}
