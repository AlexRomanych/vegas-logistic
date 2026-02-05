// Здесь будут функци для проверки чего-либо
import { DISPLAY_CATCH_ERRORS } from '@/app/constants/common.ts'
import { isRef } from 'vue'

// Проверка на успешный ответ сервера
// warning: Пока так
export function checkCRUD(data: any) {
    if (typeof data === 'string') {
        return data.toLowerCase().indexOf('success') !== -1
    } else if (typeof data === 'object') {
        if ('error' in data) {
            return  data.error === null
        } else if('status' in data) {
            return data.status === 200
        } else {
            return true
        }

    }




    return false
}


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


// ___ Проверка ответа API на коректность

interface CheckResult {
    success: boolean,
    code: number,
    error: string | null,
    data: object | null | undefined | [] | string[],
    message: string | null | undefined | string[],
}

export function checkApiAnswer(response: unknown): CheckResult | never {

    console.log('response: ', response)

    const checkResult: CheckResult = {
        success: false,
        code: 255,
        error: null,
        data: null,
        message: null
    }

    if (typeof response === 'string') {
        if (response === 'success') {
            checkResult.success = true
            checkResult.code = 0
            return checkResult
        }
    }

    // Проверяем, что response - это объект и он не null
    if (typeof response === 'object' && response !== null) {
        // Теперь проверяем, что у объекта есть свойство 'data'
        // и что это свойство равно 'success'
        if ('data' in response && response.data === 'success') {
            // TypeScript теперь знает, что response.data существует и является string
            checkResult.success = true
            checkResult.code = 0
            return checkResult
        }
    }


    // Проверяем, что response - это объект и он не null
    if (response !== null && isRef(response)) {
        // Теперь проверяем, что у объекта есть свойство 'data'
        // и что это свойство равно 'success'
        if (response.value === 'success') {
            // TypeScript теперь знает, что response.data существует и является string
            checkResult.success = true
            checkResult.code = 0
            return checkResult
        }
    }


    throw new Error('Неверный тип ответа от сервера!')

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
        console.error(message, error.message)
        console.error('Тип ошибки:', error.name)
        console.error('Стек вызовов:', error.stack)
    } else if (typeof error === 'string') {
        // Если это просто строка
        console.error(message, error)
    } else {
        // Для любых других неизвестных типов
        console.error(message, error)
    }
}


// ___ Проверяем JSON объект по шаблону
export function validateJsonByTemplate(data: object | string | any[], template: object) {

    if (typeof data === 'string') {
        try {
            data = JSON.parse(data)
            if (Array.isArray(data)) {
                data = data[0]
            }
        } catch (e) {
            console.error('Invalid JSON string:', e)
            return false
        }
    } else if (Array.isArray(data)) {
        if (data.length > 0) {
            data = data[0]
        } else {
            return false
        }
    }

    // @ts-ignore
    return Object.keys(template).every(key => template[key](data[key]))
}
