/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.vue",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        accent: '#E53935',       // your main red
        'accent-light': '#EF5350',
        'accent-dark': '#C62828',
      }
    },
  },
  plugins: [],
}