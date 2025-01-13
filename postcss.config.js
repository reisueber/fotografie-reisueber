module.exports = {
    plugins: [
        require('postcss-import'), // Für @import-Anweisungen
        require('tailwindcss'),   // Für TailwindCSS
        require('autoprefixer'),  // Für automatische Vendor-Prefixes
    ],
};