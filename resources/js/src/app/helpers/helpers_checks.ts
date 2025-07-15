// Здесь будут функци для проверки чего-либо
import { DISPLAY_CATCH_ERRORS } from '@/app/constants/common.ts'


// ___ Проверяет, является ли строка JSON
export function isJSON(str: string) {
    try {
        JSON.parse(str)
        return true
    } catch (e) {
        return false
    }
}

// ___ Проверяет, является ли ответ с сервера ошибкой
export function isResponseWithError(response: any) {
    // return response['data']['status'] === 600
    return response?.data?.status === 600
}


// ___ Проверка ответа API на кореектность
export function checkApiAnswer(response: any) {

    console.log('response: ', response)

    interface CheckResult {
        success: boolean,
        code: number,
        error: string | null,
        data: object | null | undefined | [] | string[],
        message: string | null | undefined | string[],
    }

    const checkResult: CheckResult = {
        success: false,
        code: 255,
        error: null,
        data: null,
        message: null
    }

    if (response.data === 'success') {
        checkResult.success = true
        checkResult.code = 0
    }

    return checkResult
}


// ___ Простая обертка поверх ответа от сервера
// ___ Потом будет использоваться для проверки ответа от сервера
// TODO: Переписать обработчик ошибок
export function apiAnswerCheckDecorator<T>(data: T): T {
    return data
}


// ___ Обрабатываем ошибки блока catch
export function catchErrorHandler<T = unknown>(message: string = '', error: T = null as T): void {
    if (!DISPLAY_CATCH_ERRORS) return

    if (error instanceof Error) {
        // В этом блоке e автоматически сужается до типа Error
        console.error(message, error.message);
        console.error('Тип ошибки:', error.name);
        console.error('Стек вызовов:', error.stack);
    } else if (typeof error === 'string') {
        // Если это просто строка
        console.error(message, error);
    } else {
        // Для любых других неизвестных типов
        console.error(message, error);
    }
}
