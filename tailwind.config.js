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
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
               primary: {
                    light: '#FFF5F5',  
                    DEFAULT: '#E99695', 
                    dark: '#D68281',    
                    border: '#F2D5D5',  
                },
                surface: {
                    50: '#F9FAFB',      
                    100: '#F3F4F6',     
                }
            },
        },
    },

    plugins: [forms],
};
