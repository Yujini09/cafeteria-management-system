import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
    './storage/framework/views/*.php',
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
  ],
  theme: {
    extend: {
      colors: {
        'ret-dark': '#1f2937', // Dark gray for text
        'ret-green': '#10b981', // Emerald green
        'ret-green-light': '#34d399', // Light emerald green
        'ret-green-dark': '#059669', // Dark emerald green
      }
    }
  },
  plugins: [],
}
