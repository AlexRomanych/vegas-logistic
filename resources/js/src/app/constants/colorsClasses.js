const colorValue = 300                  // value of the color

const mainColor = 'color'               // основной цвет элемента
const textColor = 'text'                // цвет текста
const effectDirection = 'effect'        // направление изменения основного цвета элемента
const toLight = 'toLight'               // изменение основного цвета элемента на более светлый
const toDark = 'toDark'               // изменение основного цвета элемента на более светлый

// Задаем классы для каждого цвета

// const warn = 'warn'
// const error = 'error'
// const notice = 'notice'
// const normal = 'normal'

const primary = 'primary'
const secondary = 'secondary'
const success = 'success'
const danger = 'danger'
const warning = 'warning'
const info = 'info'
const light = 'light'
const dark = 'dark'


const colorsClasses = {}


colorsClasses[primary] = {
    [mainColor]: 'blue',
    [textColor]: 'white',
    [effectDirection]: toLight
}
colorsClasses[secondary] = {
    [mainColor]: 'slate',
    [textColor]: 'white',
    [effectDirection]: toLight
}
colorsClasses[success] = {
    [mainColor]: 'green',
    [textColor]: 'white',
    [effectDirection]: toLight
}
colorsClasses[danger] = {
    [mainColor]: 'red',
    [textColor]: 'white',
    [effectDirection]: toLight
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
    [mainColor]: 'gray',
    [textColor]: 'black',
    [effectDirection]: toDark
}
colorsClasses[dark] = {
    [mainColor]: 'black',
    [textColor]: 'white',
    [effectDirection]: toLight
}


// colorsClasses[warn] = 'yellow-' + colorValue
// colorsClasses[warn] = 'yellow-' + colorValue
// colorsClasses[error] = 'red-' + colorValue
// colorsClasses[notice] = 'green-' + colorValue
// colorsClasses[normal] = 'gray-' + colorValue
//
//
// const colorsClasses = {}
// colorsClasses[warn] = 'yellow-' + colorValue
// colorsClasses[error] = 'red-' + colorValue
// colorsClasses[notice] = 'green-' + colorValue
// colorsClasses[normal] = 'gray-' + colorValue


const colorsList = [primary, secondary, success, danger, warning, info, light, dark]
const colorsListObj = {primary, secondary, success, danger, warning, info, light, dark}
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
    primary, secondary, success, danger, warning, info, light, dark
}
