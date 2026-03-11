import { DISPLAY_CATCH_ERRORS } from '@/app/constants/common.ts'

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
