/** @type {import('tailwindcss').Config} */

const defaultTheme = require('tailwindcss/defaultTheme');
// const defaultTheme = import('tailwindcss').Config
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
//    darkMode: false, // or 'media' or 'class'
    theme: {
        extend: {
            fontFamily: {
                'sans': ['"Raleway"', ...defaultTheme.fontFamily.sans],
                'mono': ['"Fira Code"', ...defaultTheme.fontFamily.mono],
                // 'serif': ['"Fira Code"', ...defaultTheme.fontFamily.serif],

            },
        }
    },
    variants: {
        extend: {},
    },
    plugins: [],
}
