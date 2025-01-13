/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./templates/**/*.{html,html.twig}",
    "./assets/js/**/*.{js,jsx,ts,tsx,vue}"
  ],
  theme: {
    extend: {
      colors: {
        'accent': 'rgb(245 158 11)',
        'background': '#fff'
      }
    }
  },
  plugins: []
}

