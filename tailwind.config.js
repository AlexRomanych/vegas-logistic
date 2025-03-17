const colors = require('tailwindcss/colors')
const defaultTheme = require('tailwindcss/defaultTheme');

/*
import {
    colorsClasses,
    danger, dark, info, light,
    primary,
    secondary,
    success, toDark,
    toLight,
    warning
} from "@/src/app/constants/colorsClasses.js";
*/

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
                'sans': ['Raleway', ...defaultTheme.fontFamily.sans],
                'mono': ['Fira Code', ...defaultTheme.fontFamily.mono],
                // 'serif': ['"Fira Code"', ...defaultTheme.fontFamily.serif],

            },

            fontSize: {
                'mc': ['0.625rem', { lineHeight: '0.825rem' }],   // font-mc (micro)
            },
        }
    },
    variants: {
        extend: {},
    },
    plugins: [],
    safelist: [
        'text-2xl',
        'text-3xl',
        {
            pattern: /bg-(blue|slate|green|red|yellow|sky|gray|black|zinc)-(200|300|400|500|600|700|800)/,
            variants: ['hover'],
        },
        {
            pattern: /text-(blue|slate|green|red|yellow|sky|gray|black|zinc)-(200|300|400|500|600|700|800)/,
        },
        {
            pattern: /border-(blue|slate|green|red|yellow|sky|gray|black|zinc)-(200|300|400|500|600|700|800)/,
            variants: ['hover'],
        },
        {
            pattern: /placeholder-(blue|slate|green|red|yellow|sky|gray|black|zinc)-(200|300|400|500|600|700|800)/,
        },
        {
            pattern: /ring-(blue|slate|green|red|yellow|sky|gray|black|zinc)-(200|300|400|500|600|700|800)/,
            variants: ['focus'],
        },

    ],
}





