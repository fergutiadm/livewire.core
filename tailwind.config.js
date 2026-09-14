import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './app/Http/Livewire/**/*.php',
    ],
    safelist: [
        // Backgrounds
        'bg-white', 'bg-black',
        'bg-blue-500', 'bg-green-500', 'bg-yellow-500',
        'bg-purple-500', 'bg-pink-500', 'bg-indigo-500',
        'bg-teal-500', 'bg-orange-500', 'bg-cyan-500',

        // Text colors
        'text-black', 'text-white',
      ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                accent: { DEFAULT: "#2563eb", 600: "#1d4ed8" }, // color personalizado
                base: { bg: "#f8fafc", border: "#e5e7eb" },     // colores base para fondos y bordes
            },
            container: {
                center: true,
                padding: "1rem",
                screens: { sm: "640px", md: "768px", lg: "1024px", xl: "1200px" },
            },
            boxShadow: {
                card: "0 1px 2px rgba(0,0,0,.06), 0 2px 8px rgba(0,0,0,.04)",
            },
            borderRadius: { xl: "0.9rem" },
        },
    },

    plugins: [forms, typography],
};
