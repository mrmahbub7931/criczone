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
        primary: {
          DEFAULT: '#0D47A1',
          light: '#1565C0',
          dark: '#0a2f6e',
        },
        secondary: {
          DEFAULT: '#EF5350',
          light: '#FF7043',
          dark: '#E53935',
        },
        accent: '#E53935',
        'accent-light': '#EF5350',
        'accent-dark': '#C62828',
      },
    },
  },
  plugins: [],
}
