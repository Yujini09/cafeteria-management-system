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
        'ret-dark': '#1f2937',
        'ret-green': '#10b981',
        'ret-green-light': '#34d399',
        'ret-green-dark': '#059669',
        // Admin design system (sidebar/admin UI only)
        admin: {
          primary: '#00462E',
          'primary-hover': '#057C3C',
          'primary-light': '#e8f5ee',
          secondary: '#f5f5f5',
          'secondary-hover': '#e5e5e5',
          'secondary-text': '#404040',
          danger: '#dc2626',
          'danger-hover': '#b91c1c',
          'danger-light': '#fef2f2',
          warning: '#d97706',
          'warning-light': '#fffbeb',
          success: '#059669',
          'success-light': '#ecfdf5',
          neutral: {
            50: '#fafafa',
            100: '#f5f5f5',
            200: '#e5e5e5',
            300: '#d4d4d4',
            400: '#a3a3a3',
            500: '#737373',
            600: '#525252',
            700: '#404040',
            800: '#262626',
            900: '#171717',
          },
        },
      },
      borderRadius: {
        'admin': '0.625rem',   // 10px - buttons, inputs
        'admin-lg': '1rem',     // 16px - modals, cards
      },
      spacing: {
        'admin-input': '0.875rem',  // consistent form padding
        'admin-form-gap': '1rem',
      },
      fontFamily: {
        admin: ['Poppins', defaultTheme.fontFamily.sans],
      },
      boxShadow: {
        'admin': '0 4px 20px rgba(0, 0, 0, 0.04)',
        'admin-modal': '0 20px 60px rgba(0, 0, 0, 0.2)',
      },
      transitionDuration: {
        'admin': '200ms',
      },
    },
  },
  plugins: [],
}
