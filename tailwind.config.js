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
                'sans': ['Roboto', ...defaultTheme.fontFamily.sans],
                // 'sans': ['Raleway', ...defaultTheme.fontFamily.sans],
                'mono': ['Fira Code', ...defaultTheme.fontFamily.mono],
                // 'serif': ['"Fira Code"', ...defaultTheme.fontFamily.serif],

            },

            fontSize: {
                'mc': ['0.625rem', { lineHeight: '0.825rem' }],   // text-mc (micro)
                'nn': ['0.525rem', { lineHeight: '0.825rem' }],   // text-nn (nano)
                'pc': ['0.425rem', { lineHeight: '0.825rem' }],   // text-pc (pico)
            },
        }
    },
    variants: {
        extend: {},
    },
    plugins: [],
    safelist: [
        'text-mc',
        'text-nn',
        'text-pc',
        'text-2xl',
        'text-3xl',
        {
            pattern: /bg-(blue|slate|green|red|yellow|sky|gray|black|zinc|orange|stone|indigo)-(200|300|400|500|600|700|800)/,
            variants: ['hover'],
        },
        {
            pattern: /text-(blue|slate|green|red|yellow|sky|gray|black|zinc|orange|stone|indigo)-(200|300|400|500|600|700|800)/,
        },
        {
            pattern: /border-(blue|slate|green|red|yellow|sky|gray|black|zinc|orange|stone|indigo)-(200|300|400|500|600|700|800)/,
            variants: ['hover'],
        },
        {
            pattern: /placeholder-(blue|slate|green|red|yellow|sky|gray|black|zinc|orange|stone|indigo)-(200|300|400|500|600|700|800)/,
        },
        {
            pattern: /ring-(blue|slate|green|red|yellow|sky|gray|black|zinc|orange|stone|indigo)-(200|300|400|500|600|700|800)/,
            variants: ['focus'],
        },
        // 'w-[330px]',
        // 'w-[20px]',
        // 'w-[30px]',
        // 'w-[48px]',
        // 'w-[51px]',
        // 'w-[50px]',
        // 'w-[450px]',
        // 'w-[500px]',
        // 'w-[600px]',

    ],
}





