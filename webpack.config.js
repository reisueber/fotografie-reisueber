const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// This ensures compatibility with tools relying on webpack.config.js.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // Set output directory for website build
    .setOutputPath('public/build/website/')
    .setPublicPath('/build/website')

    // Entry points: CSS and JS
    .addStyleEntry('styles', './assets/website/styles/base.css') // Main CSS entry
    .addEntry('app', './assets/website/js/app.js') // Main JS entry

    // Enable features
    .enableSourceMaps(!Encore.isProduction()) // Enable source maps in development mode
    .enableVersioning(Encore.isProduction()) // Version files in production
    .cleanupOutputBeforeBuild() // Clean build folder before new builds
    .enableSingleRuntimeChunk() // Single runtime chunk for better performance

    // Enable PostCSS loader (required for Tailwind and postcss-import)
    .enablePostCssLoader()

    // Configure split chunks for better build management
    .configureSplitChunks((splitChunks) => {
        splitChunks.name = 'website/runtime';
    });

module.exports = Encore.getWebpackConfig();
