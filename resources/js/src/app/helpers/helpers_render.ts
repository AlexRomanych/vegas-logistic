// Info: Тут хелперы для рендера

import { catchErrorHandler } from '@/app/helpers/helpers_checks.ts'

// __ Loader
// import { useLoading } from 'vue-loading-overlay'
import 'vue-loading-overlay/dist/css/index.css'                     // Warning: Это нужно обязательно
import { LOADER_SETTINGS } from '@/app/constants/common.ts'

// ___ Обработчик для лоадера
type HandlerType = (data?: any) => any
type ActionType = (data?: any) => void

export async function loaderHandler(
    loadingService: any,                                        // получаем экземпляр лоадера, чтобы сохранить контекст компонента, в которм он вызывается
    handler: HandlerType,                                       // асинхронная функция
    errMessage: string = 'Ошибка асинхронной обработки...',     // сообщение об ошибке
    actions: ActionType = () => {},                             // действия после выполнения, функция без параметров
): Promise<any> | never {

    const loader = loadingService.show({...LOADER_SETTINGS})
    // console.log(loader)

    // const $loading = useLoading({...LOADER_SETTINGS})
    // const loader = $loading.show()

    try {

        const result = await handler()
        actions()

        return result
    } catch (e: unknown) {
        catchErrorHandler(errMessage, e)
        throw e
    } finally {
        if (loader) loader.hide()
    }

}

// ___ End Loader
