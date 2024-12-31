/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './templates/**/*.html.twig',
    './assets/**/*.js',
    './assets/**/*.css'
  ],
  theme: {
    extend: {
      colors: {
        background: '#fff', // Beispiel: Hellgrau
        primary: '#000',   // Beispiel: Dunkelblau
        accent: '#f59e0b',    // Beispiel: Orange
      },
    },
  },
  plugins: [],
}

