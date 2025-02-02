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

    // Kopiere Bilder von assets nach public/build
    .copyFiles({
        from: './assets/website/images',
        to: 'images/[path][name].[ext]',
        pattern: /\.(png|jpg|jpeg|svg|ico|webmanifest)$/
    })

    // Kopiere Fonts von assets nach public/build
    .copyFiles({
        from: './assets/website/fonts',
        to: 'fonts/[path][name].[ext]',
        pattern: /\.(woff|woff2)$/
    })

    // Entry point anpassen
    .addEntry('app', './assets/app.js') // Main JS entry

    // Enable features
    .enableSourceMaps(!Encore.isProduction()) // Enable source maps in development mode
    .enableVersioning(Encore.isProduction()) // Version files in production
    .cleanupOutputBeforeBuild() // Clean build folder before new builds
    .enableSingleRuntimeChunk() // Single runtime chunk for better performance

    // Enable PostCSS loader (required for Tailwind and postcss-import)
    .enablePostCssLoader()
    .enableSassLoader();

module.exports = Encore.getWebpackConfig();
