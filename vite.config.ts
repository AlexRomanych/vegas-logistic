import { fileURLToPath, URL } from 'node:url'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'
import laravel from 'laravel-vite-plugin'

// import tailwindcss from '@tailwindcss/vite'             // добавлено в версии 4.0
// import tailwindcss from '@tailwindcss/postcss' // удалено в версии 4.0
import tailwindcss from 'tailwindcss' // удалено в версии 4.0

import autoprefixer from 'autoprefixer'

// https://vite.dev/config/
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        // tailwindcss(),
        vue(),
        // vueDevTools(),
    ],
    css: {
        postcss: {
            plugins: [
                tailwindcss, // удалено в версии 4.0
                autoprefixer,
            ],
        },
    },
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./resources/js/src', import.meta.url)),
            '@css': fileURLToPath(new URL('./resources/css', import.meta.url)),
            // '@css': '/www/bmpz-logistic/resources/css',     //todo разобраться с относительными путями
        },
    },
})

// import { fileURLToPath, URL } from 'node:url'
// import { defineConfig } from 'vite'

// import vue from '@vitejs/plugin-vue'
// import vueDevTools from 'vite-plugin-vue-devtools'

// import tailwindcss from 'tailwindcss'                // удалено в версии 4.0
// import tailwindcss from '@tailwindcss/vite'             // добавлено в версии 4.0
// import autoprefixer from 'autoprefixer'
// import {fileURLToPath} from "url";

// console.log(fileURLToPath(new URL('./src', import.meta.url)))

// export default defineConfig({
//     plugins: [
//         laravel({
//             input: ['resources/css/app.css', 'resources/js/app.js'],
//             refresh: true,
//         }),
// 		vue(),
//         vueDevTools(),
//         tailwindcss(),
//     ],
//     // css: {
//     //     postcss: {
//     //         plugins: [
//     //             tailwindcss,
//     //             autoprefixer,
//     //
//     //         ],
//     //     },
//     // },
//     resolve: {
//         alias: {
//             alias: {
//                 '@': fileURLToPath(new URL('./src', import.meta.url))
//             },
//             // '@': fileURLToPath(new URL('./src', import.meta.url)),
//             // '@': '/resources/js',               // attract: Это пред установка
//             '@css': '/www/bmpz-logistic/resources/css',     //todo разобраться с относительными путями
//             // '@css': fileURLToPath(new URL('./src', import.meta.url)),
//             // '@views': 'resources/js/src/views',
//             // 'vue-router': 'vue-router/dist/vue-router.esm-bundler.js'
//         },
//     }
// });
//
