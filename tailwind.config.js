import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                lgu: {
                    blue: '#003087',   // Deep government blue
                    'blue-mid': '#0047AB',   // Cobalt blue
                    'blue-light': '#1565C0',  // Lighter blue for hover
                    gold: '#C9A84C',   // Official gold/yellow
                    'gold-light': '#F0C040',  // Bright gold accent
                    red: '#B22222',   // Philippine red
                    green: '#2E7D32',   // Rice/nature green
                    cream: '#FDF8F0',   // Warm off-white background
                    gray: '#F4F6FA',   // Light page background
                },
            },
            backgroundImage: {
                'lgu-gradient': 'linear-gradient(135deg, #003087 0%, #0047AB 60%, #1565C0 100%)',
                'gold-gradient': 'linear-gradient(135deg, #C9A84C 0%, #F0C040 100%)',
            },
            boxShadow: {
                'lgu': '0 4px 24px 0 rgba(0,48,135,0.12)',
                'lgu-lg': '0 8px 40px 0 rgba(0,48,135,0.18)',
            },
        },
    },

    plugins: [forms, typography],
};
