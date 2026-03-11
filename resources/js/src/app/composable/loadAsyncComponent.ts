import { defineAsyncComponent } from 'vue'
import { catchErrorHandler } from '@/app/composable/catchErrorHandler.ts'

// warning: Это путь от этого хелпера к корневой директории
const PATH_TO_MAIN = './../../'

/**
 * ___ Функция для динамического импорта компонента по его имени.
 * ___ Она возвращает асинхронный компонент.
 */
export const loadAsyncComponent = (componentName: string, path: string = '') => {

    const workPath = path && path.endsWith('/') ? path : path + '/'
    const workName = componentName.endsWith('.vue') ? componentName : componentName + '.vue'

    // __ Важно: Путь к компонентам должен быть известен или динамически построен
    // __ Здесь предполагается, что все компоненты находятся в папке 'components'
    // __ и имеют стандартное имя файла (например, MyComponentA.vue)
    try {
        // defineAsyncComponent - это обертка для динамического импорта
        return defineAsyncComponent({
            loader: () => import(PATH_TO_MAIN + workPath + workName),
            // Можно добавить loadingComponent, errorComponent, timeout и т.д.
            // loadingComponent: /* компонент-заглушка */,
            // errorComponent: /* компонент ошибки */,
            // delay: 200, // Задержка перед показом loadingComponent
            // timeout: 3000, // Таймаут загрузки
        })
    } catch (error) {
        catchErrorHandler(`Ошибка загрузки компонента ${componentName}:`, error)
        // Вернуть компонент ошибки или null
        return null
    }
}
