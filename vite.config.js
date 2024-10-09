import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import tailwindcss from 'tailwindcss'
import autoprefixer from 'autoprefixer'
import { fileURLToPath } from 'node:url'

// console.log(new URL('./src', import.meta.url))
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),

    ],
    css: {
        postcss: {
            plugins: [
                tailwindcss,
                autoprefixer,

            ],
        },
    },
    resolve: {
        alias: {
            // '@': fileURLToPath(new URL('./src', import.meta.url)),
            // '@': './resources/js',
            // '@': 'resources/js/src',
            // '@views': 'resources/js/src/views',
            // 'vue-router': 'vue-router/dist/vue-router.esm-bundler.js'
        },
    }
});

// vite.config.js
// import { defineConfig } from 'vite';
// import laravel from 'laravel-vite-plugin';
// import tailwindcss from 'tailwindcss';
// import autoprefixer from 'autoprefixer';

// export default defineConfig({
//     plugins: [
//         laravel({
//
//             input: ['resources/css/app.css', 'resources/js/app.js'],
//             refresh: true,
//         }),
//     ],
//     css: {
//         postcss: {
//             plugins: [
//                 tailwindcss,
//                 autoprefixer,
//
//             ],
//         },
//     },
// });

// export default defineConfig({
//     plugins: [
//         laravel({
//             input: ['resources/css/app.css', 'resources/js/app.js'],
//             refresh: true,
//         }),
//         vue({
//             template: {
//                 transformAssetUrls: {
//                     base: null,
//                     includeAbsolute: false,
//                 },
//             },
//
//         })
//     ],
//     resolve: {
//         alias: {
//             '@': '/src',
//             'vue-router': 'vue-router/dist/vue-router.esm-bundler.js'
//         },
//     }
// });
