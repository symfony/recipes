var Encore = require('@symfony/webpack-encore');

Encore
    // directory where all compiled assets will be stored
    .setOutputPath('web/build/')
    // what's the public path to this directory (relative to your project's document root dir)
    .setPublicPath('/build')
    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()
    // allow sass/scss files to be processed
    .enableSassLoader()
    .enableSourceMaps(!Encore.isProduction())
    // will output as web/build/app.js
    // .addEntry('app', './assets/js/app.js')
    // will output as web/build/global.css
    // .addStyleEntry('global', './assets/css/app.scss')
    // allow legacy applications to use $/jQuery as a global variable
    // .autoProvidejQuery()
    // create hashed filenames (e.g. app.abc123.css)
    // .enableVersioning()
;

module.exports = Encore.getWebpackConfig();
