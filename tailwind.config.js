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
                sans: ['DM Sans', ...defaultTheme.fontFamily.sans],
                display: ['Playfair Display', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                kafe: {
                    50: '#fff5f9',
                    100: '#ffe8f2',
                    200: '#ffd1e6',
                    300: '#ffb3d4',
                    400: '#ff85bc',
                    500: '#f0529c',
                    600: '#e03484',
                    700: '#c2186b',
                    800: '#a01558',
                    900: '#831447',
                    950: '#50072a',
                },
            },
        },
    },

    plugins: [forms],
};
