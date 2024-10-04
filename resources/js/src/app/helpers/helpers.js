import {colorsClasses, colorsListObj, colorsList} from "@/src/app/constants/colorsClasses.js"

export function getColorClassByType(type) {
    // console.log(colorsClasses, colorsClasses['danger'].color)
    return '-' + colorsClasses[type].color + '-'
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
