const colorValue = 300                  // value of the color

// Задаем классы для каждого цвета

const warn = 'warn'
const error = 'error'
const notice = 'notice'
const normal = 'normal'

const colorsClasses = {}
colorsClasses[warn] = 'yellow-' + colorValue
colorsClasses[error] = 'red-' + colorValue
colorsClasses[notice] = 'green-' + colorValue
colorsClasses[normal] = 'gray-' + colorValue

const colorsList = [warn, error, notice, normal]
//
// const colorsClasses = {
//     warn: 'yellow-' + colorValue,
//     error: 'red-' + colorValue,
//     notice: 'green-' + colorValue,
//     default: 'gray-' + colorValue
// }


export {colorsClasses, colorsList}
