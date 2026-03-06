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

    safelist: [
        'hover:border-primary',
        'hover:text-primary',
        'hover:shadow-sm',
        'hover:bg-red-50',
        'hover:text-red-600',
        'hover:border-red-100',
        'hover:bg-primary-dark',
        'hover:shadow-md',
        'hover:bg-red-700',
        'hover:bg-red-800',
        'hover:opacity-80',
        'hover:bg-blue-500',
        'active:scale-95',
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
