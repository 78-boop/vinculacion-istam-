import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                indigo: {
                    50: '#e6f4ee',
                    100: '#c2e3d3',
                    200: '#9ad1b6',
                    300: '#71bf99',
                    400: '#4fb182',
                    500: '#2da36b',
                    600: '#006B47',
                    700: '#005c3d',
                    800: '#004d33',
                    900: '#003d29',
                },
                'istam-light': '#8BC34A',
            },
        },
    },

    plugins: [forms],
};