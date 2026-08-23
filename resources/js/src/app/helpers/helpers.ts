
export * from '@/app/helpers/helpers_render.js'

// import { DISPLAY_CONSOLE_LOG } from '@/app/constants/common.ts'

import type { ColorName, EffectDirection } from '@/app/constants/colorsClasses.js'

import {
    colorsClasses, toDark, /*toLight,*/ colorIndex, colorIndexOffset, colorIndexLight
} from '@/app/constants/colorsClasses.js'

import {
    fontPico, fontNano, fontMicro, fontMini, fontSmall, fontNormal, fontLarge, fontHuge, /*fontSizesList*/
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
    mainIndexColor     = type === 'light' ? colorIndexLight : mainIndexColor

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
    switch (param) {
        case fontPico:
            textSizeClass = 'text-pc'
            break
        case fontNano:
            textSizeClass = 'text-nn'
            break
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


/**
 * ___ Получаем класс для скругления рамки
 * @param param
 */
export function getRoundedClass(param: string) {
    switch (param) {
        case '2':
            return 'rounded-[2px]'
        case '3':
            return 'rounded-[3px]'
        case '4':
            return 'rounded-[4px]'
        case '5':
            return 'rounded-[5px]'
        case '6':
            return 'rounded-[6px]'
        case '7':
            return 'rounded-[7px]'
        case '8':
            return 'rounded-[8px]'
        case '9':
            return 'rounded-[9px]'
        case '10':
            return 'rounded-[10px]'
        default:
            return param
    }
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
// export function log_Var2(...args: any[]): void {
//     if (DISPLAY_CONSOLE_LOG) {
//         const stackTrace = new Error().stack
//
//         if (stackTrace) {
//             const lines = stackTrace.split('\n')
//             const callerLine = lines[2] // Строка с информацией о вызывающей функции
//
//             // Используем регулярное выражение для извлечения имени файла и номера строки
//             // .*\/ — это "любые символы, за которыми следует слэш"
//             const match = callerLine.match(/([^\/]+):(\d+):(\d+)\)$/)
//
//             // Если не удается найти шаблон в конце строки, пробуем другой формат (Node.js)
//             if (!match) {
//                 // Пример:    at someFunction (internal/modules/cjs/loader.js:100:20)
//                 // Регулярка:  ^ at [^ ]+ \((.+):(\d+):(\d+)\)
//                 const nodeMatch = callerLine.match(/\(([^)]+)\)$/)
//                 if (nodeMatch) {
//                     const fullPath = nodeMatch[1]
//                     const parts = fullPath.split(':')
//                     const fileName = parts[0].split('/').pop() // Получаем имя файла из полного пути
//                     const lineNumber = parts[1]
//                     console.log(`[${fileName}:${lineNumber}]`, ...args)
//                     return
//                 }
//             }
//
//             if (match) {
//                 const fullPath = match[1]
//                 const fileName = fullPath.split('/').pop() // Получаем имя файла из полного пути
//                 const lineNumber = match[2]
//                 console.log(`[${fileName}:${lineNumber}]`, ...args)
//             } else {
//                 console.log('Unable to parse caller stack trace:', callerLine, ...args)
//             }
//         } else {
//             console.log(...args)
//         }
//     }
// }


// ___ Показывать ли в консоли логи
// export function log_FullPath(...args: any[]): void {
//     if (DISPLAY_CONSOLE_LOG) {
//         // Создаем новый объект ошибки, чтобы получить стек вызовов
//         const stackTrace = new Error().stack
//
//         if (stackTrace) {
//             // Разделяем стек на строки и находим нужную
//             // В зависимости от окружения (браузер/Node.js) формат стека может отличаться.
//             // Строка [1] обычно содержит информацию о файле и строке, где была вызвана функция log.
//             const lines = stackTrace.split('\n')
//             const callerLine = lines[2] // lines[0] — "Error", lines[1] — "at log", lines[2] — вызывающая функция
//
//             // Используем регулярное выражение для извлечения имени файла и номера строки
//             const match = callerLine.match(/\((.*):(\d+):(\d+)\)$/)
//             if (match) {
//                 const filePath = match[1]
//                 const lineNumber = match[2]
//                 // Выводим информацию
//                 console.log(`[${filePath}:${lineNumber}]`, ...args)
//             } else {
//                 // Если не удалось распарсить, выводим просто стек
//                 console.log('Caller stack trace:', callerLine, ...args)
//             }
//         } else {
//             // Если стек не доступен, выводим как обычно
//             console.log(...args)
//         }
//     }
// }


// ___ Показывать ли в консоли логи
// export function log(...args: any[]): void {
//     if (DISPLAY_CONSOLE_LOG) {
//         console.log(...args)
//     }
// }



// --- -------------------------------------------------------------------------
// --- ------------------------------ Цвета-------------------------------------
// --- -------------------------------------------------------------------------

// __ Возвращает цвет по %
export function getColorByPercent(percent: number) {
    // 1. Ограничиваем значение в диапазоне от 0 до 100
    const clampedPercent = Math.max(0, Math.min(100, percent));

    // 2. Вычисляем тон (Hue): 0% = 0 (красный), 100% = 120 (зеленый)
    const hue = (clampedPercent * 120) / 100;
    const saturation = 100; // Насыщенность (100% для ярких цветов)
    const lightness = 45;   // Яркость (45-50% дает сочные цвета)

    // 3. Конвертируем HSL в HEX
    return hslToHex(hue, saturation, lightness);
}

// __ Вспомогательная функция конвертации HSL -> HEX
export function hslToHex(h: number, s: number, l: number) {
    l /= 100
    const a = (s * Math.min(l, 1 - l)) / 100
    const f = (n: number) => {
        const k = (n + h / 30) % 12
        const color = l - a * Math.max(Math.min(k - 3, 9 - k, 1), -1)
        return Math.round(255 * color).toString(16).padStart(2, '0')
    };
    return `#${f(0)}${f(8)}${f(4)}`.toUpperCase()
}


// __ Вспомогательная функция, делит строку примерно на 2 равные части по пробелу
export function splitStringInHalf(str: string): [string, string] {
    if (!str) return ['', '']

    // __ Ищем индекс середины строки
    const middle = Math.floor(str.length / 2)

    // __ Ближайшие пробелы слева и справа от середины
    const spaceBefore = str.lastIndexOf(' ', middle)
    const spaceAfter = str.indexOf(' ', middle)

    // __ Если пробелов вообще нет в строке
    if (spaceBefore === -1 && spaceAfter === -1) {
        return [str, '']
    }

    let splitIndex = -1

    // __ Выбираем пробел, который ближе к физическому центру строки
    if (spaceBefore === -1) {
        splitIndex = spaceAfter
    } else if (spaceAfter === -1) {
        splitIndex = spaceBefore
    } else {
        splitIndex = (middle - spaceBefore <= spaceAfter - middle)
            ? spaceBefore
            : spaceAfter
    }

    // __ Делим строку, вырезая найденный разделительный пробел
    const part1 = str.slice(0, splitIndex).trim()
    const part2 = str.slice(splitIndex + 1).trim()

    return [part1, part2]
}

// __ Разбиваем строку на 2 или 3 части в зависимости от переданной длины
export function splitString(str: string, maxLength: number = 0) {
    if (!str) return []

    // __ Если строка короче порога — делим на 2 части
    if (maxLength === 0 || str.length < maxLength) {
        return splitIntoN(str, 2)
    }

    // __ Иначе делим на 3 части
    return splitIntoN(str, 3)
}

// __ Вспомогательная функция для деления строки на N примерно равных частей по пробелу
function splitIntoN(str: string, n: number) {
    const totalLength = str.length
    const targetLength = totalLength / n

    const indices = []
    let searchStart = 0

    // __ Находим n - 1 точек деления
    for (let i = 1; i < n; i++) {
        const idealPoint = Math.round(targetLength * i)

        // __ Ищем ближайший пробел слева и справа от идеальной точки
        const spaceBefore = str.lastIndexOf(' ', idealPoint)
        const spaceAfter = str.indexOf(' ', idealPoint)

        let bestSpace = -1

        if (spaceBefore <= searchStart && spaceAfter === -1) {
            // __ Пробелов в доступной области нет
            bestSpace = idealPoint
        } else if (spaceBefore <= searchStart) {
            bestSpace = spaceAfter
        } else if (spaceAfter === -1) {
            bestSpace = spaceBefore
        } else {
            // __ Выбираем тот, что ближе к идеальной точке
            bestSpace = (idealPoint - spaceBefore <= spaceAfter - idealPoint)
                ? spaceBefore
                : spaceAfter
        }

        indices.push(bestSpace)
        searchStart = bestSpace
    }

    // __ Разбиваем строку по найденным индексам
    const result = []
    let lastIndex = 0

    for (const index of indices) {
        result.push(str.slice(lastIndex, index).trim())
        lastIndex = index + 1
    }
    // __ Добавляем последний кусок
    result.push(str.slice(lastIndex).trim())

    return result
}


// __ Разбиваем строку на 2 или 3 части в зависимости от переданной длины с учетом длины слов
export function splitStringByWeight(str: string, maxLength: number = 0): string[] {
    if (!str || !str.trim()) return []

    const words = str.trim().split(/\s+/)
    if (words.length === 1) return [str]

    const targetPartsCount = (str.length < maxLength || maxLength === 0) ? 2 : 3

    if (words.length <= targetPartsCount) {
        return words
    }

    if (targetPartsCount === 2) {
        return getBestSplit2(words)
    }

    return getBestSplit3(words)
}

function getBestSplit2(words: string[]): string[] {
    let bestResult = null
    let minScore = Infinity

    for (let i = 1; i < words.length; i++) {
        const line1 = words.slice(0, i).join(' ')
        const line2 = words.slice(i).join(' ')

        const score = Math.max(line1.length, line2.length) + Math.abs(line1.length - line2.length)

        if (score < minScore) {
            minScore = score
            bestResult = [line1, line2]
        }
    }

    return bestResult || []
}

function getBestSplit3(words: string[]): string[] {
    let bestResult = null
    let minScore = Infinity

    for (let i = 1; i < words.length - 1; i++) {
        for (let j = i + 1; j < words.length; j++) {
            const line1 = words.slice(0, i).join(' ')
            const line2 = words.slice(i, j).join(' ')
            const line3 = words.slice(j).join(' ')

            const l1 = line1.length
            const l2 = line2.length
            const l3 = line3.length

            const maxLen = Math.max(l1, l2, l3)
            const minLen = Math.min(l1, l2, l3)

            // Основной вес — максимальная длина строки + разница между самой длинной и самой короткой
            // Это жестко подавляет появление одиночных мелких слов вроде "(1500)" на отдельной строке
            const score = maxLen * 2 + (maxLen - minLen)

            if (score < minScore) {
                minScore = score
                bestResult = [line1, line2, line3]
            }
        }
    }

    return bestResult || []
}

// export function splitStringByWeight_Old(str: string, maxLength: number = 0) {
//     if (!str || !str.trim()) return []
//
//     // __ Извлекаем все слова
//     const words = str.trim().split(/\s+/)
//     if (words.length === 1) return [str]
//
//     // __ Определяем целевое количество строк (2 или 3)
//     const targetPartsCount = (str.length < maxLength || maxLength === 0) ? 2 : 3
//
//     // __ Если слов меньше, чем частей — отдаем по слову на строку
//     if (words.length <= targetPartsCount) {
//         return words
//     }
//
//     // console.log(str, targetPartsCount)
//
//     // __ Считаем общую длину всех слов с учетом одиночных пробелов между ними
//     const totalWeight = words.reduce((sum, word) => sum + word.length, 0) + (words.length - 1)
//     const targetWeightPerLine = totalWeight / targetPartsCount
//
//     const result = []
//     let currentLineWords: string[] = []
//     let currentLineLength = 0
//
//     for (let i = 0; i < words.length; i++) {
//         const word = words[i]
//         const wordLength = word.length
//
//         // __ Если это самое первое слово в строке — просто добавляем его
//         if (currentLineWords.length === 0) {
//             currentLineWords.push(word)
//             currentLineLength = wordLength
//             continue
//         }
//
//         // __ Длина текущей строки, если мы добавим это слово (с учетом пробела)
//         const lengthWithWord = currentLineLength + 1 + wordLength
//
//         // __ Сколько еще строк нам осталось сформировать
//         const remainingPartsNeeded = targetPartsCount - result.length
//         const remainingWordsCount = words.length - i
//
//         // __ Проверяем: стоит ли перенести слово на новую строку?
//         // __ Переносим, если:
//         // __ 1. Оставшихся строк строго хватает для оставшихся слов.
//         // __ 2. Текущая строка уже ближе к целевому «весу», чем если мы добавим в нее новое слово.
//         const currentDiff = Math.abs(currentLineLength - targetWeightPerLine)
//         const nextDiff = Math.abs(lengthWithWord - targetWeightPerLine)
//
//         if (remainingWordsCount >= remainingPartsNeeded && nextDiff > currentDiff) {
//             // __ Фиксируем текущую строку и начинаем новую
//             result.push(currentLineWords.join(' '))
//             currentLineWords = [word]
//             currentLineLength = wordLength
//
//             // __ Если осталась только 1 последняя целевая строка — сбрасываем в нее все оставшиеся слова
//             if (result.length === targetPartsCount - 1) {
//                 const tail = words.slice(i).join(' ')
//                 result.push(tail)
//                 return result
//             }
//         } else {
//             // __ Добавляем слово в текущую строку
//             currentLineWords.push(word)
//             currentLineLength = lengthWithWord
//         }
//     }
//
//     if (currentLineWords.length > 0) {
//         result.push(currentLineWords.join(' '))
//     }
//
//     return result
// }

