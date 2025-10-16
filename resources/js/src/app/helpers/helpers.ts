export * from '@/app/helpers/helpers_render.js'

import {DISPLAY_CONSOLE_LOG} from '@/app/constants/common.ts'

import type {ColorName, EffectDirection}  from '@/app/constants/colorsClasses.js'

import {
    colorsClasses, toDark, toLight, colorIndex, colorIndexOffset, colorIndexLight
} from '@/app/constants/colorsClasses.js'

import {
    fontMicro, fontMini, fontSmall, fontNormal, fontLarge, fontHuge, fontSizesList
} from '@/app/constants/fontSizes.js'

// Функция возвращает классы для типов кнопок (primary, secondary, success, danger, warning, info)
// prefix - префикс для класса tailwind ('bg', 'text', 'border')
// colorIndexData - индекс цвета для кнопки
// hover - менять ли цвет кнопки при наведении
export function getColorClassByType(type: ColorName, prefix = '', colorIndexData = 0, hover = true) {

    if (prefix === '') {
        return '-' + colorsClasses[type].color + '-'
    }

    let mainIndexColor = colorIndexData ? colorIndexData : colorIndex
    mainIndexColor = type === 'light' ? colorIndexLight : mainIndexColor

    const offset = colorIndexOffset

    let newIndex = 0

    if (getColorSchemeByEffect(colorsClasses[type].effect)) {
        newIndex = mainIndexColor + offset
    } else {
        newIndex = mainIndexColor - offset
    }

    const simpleClass = prefix + '-' + colorsClasses[type].color + '-' + mainIndexColor.toString()

    if (!hover) {
        return simpleClass
    }
    const hoverClass = 'hover:' + prefix + '-' + colorsClasses[type].color + '-' + newIndex.toString()

    // if (type === 'dark') {
    //     console.log(colorIndexLight, newIndex)
    //     console.log(simpleClass)
    //     console.log(hoverClass)
    // }
    return simpleClass + ' ' + hoverClass

}

export function getTextColorClassByType(type: ColorName) {
    // console.log(colorsClasses, colorsClasses['danger'].color)
    // if (type === 'plug') return 'text-slate-100'
    return 'text-' + colorsClasses[type].text
}

function getColorSchemeByEffect(effect: EffectDirection) {
    return effect === toDark
}

// console.log(colorsListObj)
// console.log(...colorsList)
// console.log(colorsClasses)

// const res = getTailwindClassByDetails({type: 'danger', objectType: 'text', colorIndex: 600})

// console.log(res)

//
// export function getTailwindClassByDetails({type, objectType, colorIndex, action}) {
//     const colorVariants = {
//         background:{
//             primary: {
//                 100: ""
//             }
//         }
//     }
//
//     console.log(colorVariants)
//
//     return undefined
// }
// ----------------------------------------------------------------------------------------------------------
// ----------------------------------------------------------------------------------------------------------

// Тут про размеры шрифтов
export function getFontSizeClass(param = fontNormal) {

    let textSizeClass = ''
    switch(param) {
        case fontMicro:
            textSizeClass = 'text-mc'
            break
        case fontMini:
            textSizeClass = 'text-xs'
            break
        case fontSmall:
            textSizeClass = 'text-sm'
            break
        case fontNormal:
            textSizeClass = 'text-base'
            break
        case fontLarge:
            textSizeClass = 'text-lg'
            break
        case fontHuge:
            textSizeClass = 'text-xl'
            break
        default:
            textSizeClass = 'text-base'
            break
    }

    return textSizeClass
}


// ___ Показывать ли в консоли логи
// export function log(...args: any[]): void {
//     if (DISPLAY_CONSOLE_LOG) {
//         const stackTrace = new Error().stack;
//
//         if (stackTrace) {
//             const lines = stackTrace.split('\n');
//             // В зависимости от окружения (браузер/Node.js) формат стека может отличаться.
//             // Строка [2] почти всегда указывает на вызывающий код.
//             const callerLine = lines[2];
//
//             // Регулярное выражение для извлечения имени файла и номера строки
//             // Это более простое и универсальное решение
//             const match = /at (.+?):(\d+):(\d+)/.exec(callerLine);
//
//             if (match && match.length >= 3) {
//                 // Извлекаем полный путь к файлу
//                 const fullPath = match[1];
//                 // Получаем только имя файла, отсекая путь
//                 const fileName = fullPath.split('/').pop().split('\\').pop();
//                 const lineNumber = match[2];
//
//                 console.log(`[${fileName}:${lineNumber}]`, ...args);
//             } else {
//                 // Если не удалось распарсить, выводим стек без форматирования
//                 console.log('Unable to parse stack trace:', callerLine);
//                 console.log(...args);
//             }
//         } else {
//             console.log(...args);
//         }
//     }
// }


// ___ Показывать ли в консоли логи
export function log_Var2(...args: any[]): void {
    if (DISPLAY_CONSOLE_LOG) {
        const stackTrace = new Error().stack;

        if (stackTrace) {
            const lines = stackTrace.split('\n');
            const callerLine = lines[2]; // Строка с информацией о вызывающей функции

            // Используем регулярное выражение для извлечения имени файла и номера строки
            // .*\/ — это "любые символы, за которыми следует слэш"
            const match = callerLine.match(/([^\/]+):(\d+):(\d+)\)$/);

            // Если не удается найти шаблон в конце строки, пробуем другой формат (Node.js)
            if (!match) {
                // Пример:    at someFunction (internal/modules/cjs/loader.js:100:20)
                // Регулярка:  ^ at [^ ]+ \((.+):(\d+):(\d+)\)
                const nodeMatch = callerLine.match(/\(([^)]+)\)$/);
                if (nodeMatch) {
                    const fullPath = nodeMatch[1];
                    const parts = fullPath.split(':');
                    const fileName = parts[0].split('/').pop(); // Получаем имя файла из полного пути
                    const lineNumber = parts[1];
                    console.log(`[${fileName}:${lineNumber}]`, ...args);
                    return;
                }
            }

            if (match) {
                const fullPath = match[1];
                const fileName = fullPath.split('/').pop(); // Получаем имя файла из полного пути
                const lineNumber = match[2];
                console.log(`[${fileName}:${lineNumber}]`, ...args);
            } else {
                console.log('Unable to parse caller stack trace:', callerLine, ...args);
            }
        } else {
            console.log(...args);
        }
    }
}


// ___ Показывать ли в консоли логи
export function log_FullPath(...args: any[]): void {
    if (DISPLAY_CONSOLE_LOG) {
        // Создаем новый объект ошибки, чтобы получить стек вызовов
        const stackTrace = new Error().stack;

        if (stackTrace) {
            // Разделяем стек на строки и находим нужную
            // В зависимости от окружения (браузер/Node.js) формат стека может отличаться.
            // Строка [1] обычно содержит информацию о файле и строке, где была вызвана функция log.
            const lines = stackTrace.split('\n');
            const callerLine = lines[2]; // lines[0] — "Error", lines[1] — "at log", lines[2] — вызывающая функция

            // Используем регулярное выражение для извлечения имени файла и номера строки
            const match = callerLine.match(/\((.*):(\d+):(\d+)\)$/);
            if (match) {
                const filePath = match[1];
                const lineNumber = match[2];
                // Выводим информацию
                console.log(`[${filePath}:${lineNumber}]`, ...args);
            } else {
                // Если не удалось распарсить, выводим просто стек
                console.log('Caller stack trace:', callerLine, ...args);
            }
        } else {
            // Если стек не доступен, выводим как обычно
            console.log(...args);
        }
    }
}



// ___ Показывать ли в консоли логи
export function log(...args: any[]): void {
    if (DISPLAY_CONSOLE_LOG) {
        console.log(...args)
    }
}
