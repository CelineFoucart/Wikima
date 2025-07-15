const Encore = require('@symfony/webpack-encore');
const CopyPlugin = require("copy-webpack-plugin");

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('base', './assets/template/base/base.js')
    .addEntry('admin', './assets/template/admin/admin.js')
    .addEntry('map', './assets/template/map/map.js')
    .addEntry('choicejs', './assets/template/choicejs/choicejs.js')
    .addEntry('sortable', './assets/template/sortable/sortableAction.js')
    .addEntry('coloris', './assets/template/coloris/coloris.js')
    .addEntry('editor', './assets/template/editor/editor.js')
    .addEntry('timeline_show', './assets/template/timeline/show.js')
    .addEntry('vue', './assets/vue/app.js')

    // Alias
    .addAliases({
        '@root': `${__dirname}/assets`,
        '@store': `${__dirname}/assets/vue/store`,
        '@components': `${__dirname}/assets/vue/components`,
        '@functions': `${__dirname}/assets/vue/functions`,
    })

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    .enableVueLoader()

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    .enableStimulusBridge('./assets/controllers.json')

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // enables Sass/SCSS support
    //.enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use React
    //.enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    //.autoProvidejQuery()
    .addPlugin(
        new CopyPlugin({
            patterns: [
                { from: './node_modules/tinymce/themes', to: './themes' },
                { from: './node_modules/tinymce/skins', to: './skins' }
            ]
        })
    )
;

module.exports = Encore.getWebpackConfig();
