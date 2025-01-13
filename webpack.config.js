const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// This ensures compatibility with tools relying on webpack.config.js.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // Korrigiere den Output-Pfad
    .setOutputPath('public/build/website/')
    .setPublicPath('/build/website')

    // Entry point anpassen
    .addEntry('app', './assets/app.js') // Main JS entry

    // Enable features
    .enableSourceMaps(!Encore.isProduction()) // Enable source maps in development mode
    .enableVersioning(Encore.isProduction()) // Version files in production
    .cleanupOutputBeforeBuild() // Clean build folder before new builds
    .enableSingleRuntimeChunk() // Single runtime chunk for better performance

    // Enable PostCSS loader (required for Tailwind and postcss-import)
    .enablePostCssLoader();

module.exports = Encore.getWebpackConfig();
