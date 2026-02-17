import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
                primary: {
                    navy: '#0F2A44',
                    DEFAULT: '#0F2A44',
                },
                accent: {
                    orange: '#F28C28',
                    DEFAULT: '#F28C28',
                },
                background: {
                    light: '#F4F6F8',
                },
            },
            fontFamily: {
                sans: ['Inter', 'Cairo', ...defaultTheme.fontFamily.sans],
                arabic: ['Cairo', 'Tajawal', 'sans-serif'],
            },
        },
    },

    plugins: [forms],
};
