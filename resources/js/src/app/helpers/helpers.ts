export * from '@/app/helpers/helpers_render.js'


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
export function getColorClassByType(type, prefix = '', colorIndexData = 0, hover = true) {

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

export function getTextColorClassByType(type) {
    // console.log(colorsClasses, colorsClasses['danger'].color)
    return 'text-' + colorsClasses[type].text
}

function getColorSchemeByEffect(effect) {
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

