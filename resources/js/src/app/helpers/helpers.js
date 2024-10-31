import {
    colorsClasses, toDark, toLight, colorIndex, colorIndexOffset, colorIndexLight
} from "@/src/app/constants/colorsClasses.js"

import {PERIOD_LENGTH} from "@/src/app/constants/dates.js"

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
// __________________________________________________________________________________________________________
// возвращает отображаемый период производства
export function getPeriod() {
    const tempDate = new Date()
    const day = tempDate.getDate()
    const month = tempDate.getMonth()
    const year = tempDate.getFullYear()

    const periodStart = new Date(year, month, '01')
    const periodEnd = new Date(year, month + PERIOD_LENGTH, '00')

    const periodStartText_Day = periodStart.getDate() < 10 ? '0' + periodStart.getDate().toString() : periodStart.getDate().toString()
    const periodStartText_Month = periodStart.getMonth() < 9 ? '0' + (periodStart.getMonth() + 1).toString() : (periodStart.getMonth() + 1).toString()

    const periodEndText_Day = periodEnd.getDate() < 10 ? '0' + periodEnd.getDate().toString() : periodEnd.getDate().toString()
    const periodEndText_Month = periodEnd.getMonth() < 9 ? '0' + (periodEnd.getMonth() + 1).toString() : (periodEnd.getMonth() + 1).toString()

    const periodStartText = periodStart.getFullYear().toString() + '-' + periodStartText_Month + '-' + periodStartText_Day
    const periodEndText = periodEnd.getFullYear().toString() + '-' + periodEndText_Month + '-' + periodEndText_Day

    return {periodStart, periodEnd, periodStartText, periodEndText}
}
