const colorIndex = 500                  // value of the color
const colorIndexLight = 200             // value of the color of light scheme
const colorIndexOffset = 100            // смещение цвета для наведения курсора

const mainColor = 'color'               // основной цвет элемента
const textColor = 'text'                // цвет текста
const effectDirection = 'effect'        // направление изменения основного цвета элемента
const toLight = 'toLight'               // изменение основного цвета элемента на более светлый
const toDark = 'toDark'               // изменение основного цвета элемента на более светлый

// Задаем классы для каждого цвета

const primary = 'primary'
const secondary = 'secondary'
const success = 'success'
const danger = 'danger'
const warning = 'warning'
const info = 'info'
const light = 'light'
const dark = 'dark'

const slate = 'slate'

const colorsClasses = {}


colorsClasses[primary] = {
    [mainColor]: 'blue',
    [textColor]: 'white',
    [effectDirection]: toDark
}
colorsClasses[secondary] = {
    [mainColor]: 'slate',
    [textColor]: 'white',
    [effectDirection]: toDark
}
colorsClasses[success] = {
    [mainColor]: 'green',
    [textColor]: 'black',
    // [textColor]: 'white',
    [effectDirection]: toDark
}
colorsClasses[danger] = {
    [mainColor]: 'red',
    [textColor]: 'white',
    [effectDirection]: toDark
}
colorsClasses[warning] = {
    [mainColor]: 'yellow',
    [textColor]: 'black',
    [effectDirection]: toLight
}
colorsClasses[info] = {
    [mainColor]: 'sky',
    [textColor]: 'black',
    [effectDirection]: toLight
}
colorsClasses[light] = {
    [mainColor]: 'zinc',
    [textColor]: 'black',
    [effectDirection]: toDark
}
colorsClasses[dark] = {
    [mainColor]: 'slate',
    [textColor]: 'white',
    [effectDirection]: toLight
}
colorsClasses[slate] = {
    [mainColor]: 'slate',
    [textColor]: 'white',
    [effectDirection]: toLight
}



const colorsList = [primary, secondary, success, danger, warning, info, light, dark, slate]
const colorsListObj = {primary, secondary, success, danger, warning, info, light, dark, slate}
//
// const colorsClasses = {
//     warn: 'yellow-' + colorValue,
//     error: 'red-' + colorValue,
//     notice: 'green-' + colorValue,
//     default: 'gray-' + colorValue
// }


export {
    colorsClasses, colorsList, colorsListObj,
    toLight, toDark,
    primary, secondary, success, danger, warning, info, light, dark, slate,
    colorIndex, colorIndexLight, colorIndexOffset
}
