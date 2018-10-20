var Encore = require('@symfony/webpack-encore');

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')


    /*
     * ENTRY CONFIG
     *
     */
    .addEntry('js/app', './assets/js/app.js')
    .addStyleEntry('css/app', './assets/scss/app.scss')

    /*
     * FEATURE CONFIG
     *
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
