import { createApp } from 'vue'
import App from './src/App.vue'
import router from './src/router/router'
import { createPinia } from 'pinia'
// import '../../resources/css/app.css'

// import { fileURLToPath } from 'url'

// console.log(fileURLToPath(new URL('./src', import.meta.url)))

const pinia = createPinia()

// console.log(import.meta.url)
// console.log(import.meta)

createApp(App)
    .use(pinia)
    .use(router)
    .mount('#app')

