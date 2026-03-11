import { DISPLAY_CONSOLE_LOG } from '@/app/constants/common.ts'

// ___ Показывать ли в консоли логи
export const log = (...args: any[]): void => {
    if (DISPLAY_CONSOLE_LOG) {
        console.log(...args)
    }
}
