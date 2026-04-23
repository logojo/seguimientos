/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./app/Livewire/**/*.php",
    ],

    safelist: [
        'text-primary',
        'text-accent',
        'text-error',

        'tooltip-primary',
        'tooltip-accent',
        'tooltip-error',

        'btn-primary',
        'btn-accent',
        'btn-error',
    ],

    theme: {
        extend: {},
    },

    plugins: [
        require('daisyui'),
    ],

    daisyui: {
        themes: ["light", "dark"], // o el que uses
    }
}