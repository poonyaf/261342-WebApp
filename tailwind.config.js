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
                'font-family': ['Noto Sans Thai', 'Quicksand', 'sans-serif', ...defaultTheme.fontFamily.sans],

            },
            colors: {
                // Pink Page
                'pink-primary': 'var(--pinkPage-primary)',
                'pink-secondary': 'var(--pinkPage-secondary)',
                'pink-accent': 'var(--pinkPage-accent)',
                'pink-brown': 'var(--pinkPage-neutral-Brown)',
                'pink-n1': 'var(--pinkPage-neutral-1)',
                'pink-n2': 'var(--pinkPage-neutral-2)',
                'pink-star': 'var(--pinkPage-favorite-star)',

                // Brown Page
                'brown-primary': 'var(--brownPage-primary)',
                'brown-secondary': 'var(--brownPage-secondary)',
                'brown-accent-pink': 'var(--brownPage-accent-pink)',
                'brown-star': 'var(--brownPage-favorite-star)',

                // Shared & Semantic
                'stellar-white': 'var(--stellar-white)',
                'bg-light': 'var(--bg-light)',
                'bg-dark': 'var(--bg-dark)',
                'success': 'var(--color-success)',
                'error': 'var(--color-error)',
            },
            borderRadius: {
                'sm': '0.375rem',
                'md': '0.5rem',
                'lg': 'var(--radius-lg)',
                'xl': 'var(--radius-xl)',
                '2xl': 'var(--radius-2xl)',
            },
            boxShadow: {
                'stellar-sm': '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
                'stellar-md': '0 4px 6px -1px rgba(0, 0, 0, 0.1)',
                'stellar-lg': '0 10px 15px -3px rgba(0, 0, 0, 0.1)',
            }
        },
    },

    plugins: [forms],
};
