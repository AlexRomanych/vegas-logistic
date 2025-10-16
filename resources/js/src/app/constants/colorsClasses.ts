// __ Константы для значений цвета (numeric)
const colorIndex = 500                  // value of the color
const colorIndexLight = 200             // value of the color of light scheme
const colorIndexOffset = 100            // смещение цвета для наведения курсора

// __ Константы для ключей свойств
const mainColor = 'color'               // основной цвет элемента
const textColor = 'text'                // цвет текста
const effectDirection = 'effect'        // направление изменения основного цвета элемента
const toLight = 'toLight'               // изменение основного цвета элемента на более светлый
const toDark = 'toDark'                 // изменение основного цвета элемента на более светлый

// __ Константы для названий цветов
const primary = 'primary'
const secondary = 'secondary'
const success = 'success'
const danger = 'danger'
const warning = 'warning'
const info = 'info'
const light = 'light'
const dark = 'dark'
const slate = 'slate'
const orange = 'orange'
const stone = 'stone'
const indigo = 'indigo'
const plug = 'plug'


// line ----------------------------------------------------
// line ----- Литеральные типы для ключей и значений -------
// line ----------------------------------------------------

// __ 1. Тип для ключей свойств (mainColor, textColor, effectDirection)
// type ColorPropertyKey = typeof mainColor | typeof textColor | typeof effectDirection;

// __ 2. Тип для направлений эффекта (toLight, toDark)
export type EffectDirection = typeof toLight | typeof toDark;

// __ 3. Тип для названий цветов (primary, secondary, etc.)
export type ColorName =
    | typeof primary
    | typeof secondary
    | typeof success
    | typeof danger
    | typeof warning
    | typeof info
    | typeof light
    | typeof dark
    | typeof slate
    | typeof orange
    | typeof stone
    | typeof indigo
    | typeof plug

// __ 4. Тип для строковых значений цветов Tailwind CSS (blue, white, green, red, yellow, sky, zinc, indigo, etc.)
// __ Это могут быть любые строковые значения, которые используются в `mainColor` и `textColor`.
// __ Если вы ограничены конкретным набором, можно создать литеральный тип и для них.
// __ Например, если они все из Tailwind CSS, вы можете перечислить их или сделать более общий 'string'.
type TailwindColorValue =
    'blue' |
    'white' |
    'slate' |
    'green' |
    'black' |
    'red' |
    'yellow' |
    'sky' |
    'zinc' |
    'orange' |
    'stone' |
    'indigo'


// __ ----------------------------------------------------
// __ Интерфейс для структуры каждого объекта цвета
// __ ----------------------------------------------------

interface IColorDefinition {
    [mainColor]: TailwindColorValue; // Используем литеральный ключ и строгий тип значения
    [textColor]: TailwindColorValue;
    [effectDirection]: EffectDirection;
}

// __ ----------------------------------------------------
// __ Тип для объекта colorsClasses
// __ ----------------------------------------------------

type ColorsClassesMap = {
    [key in ColorName]: IColorDefinition;
};

// ----------------------------------------------------
// Инициализация объекта с типизацией
// ----------------------------------------------------

const colorsClasses: ColorsClassesMap = {
    [primary]: {
        [mainColor]: 'blue',
        [textColor]: 'white',
        [effectDirection]: toDark
    },
    [secondary]: {
        [mainColor]: 'slate',
        [textColor]: 'white',
        [effectDirection]: toDark
    },
    [success]: {
        [mainColor]: 'green',
        [textColor]: 'black',
        // [textColor]: 'white',
        [effectDirection]: toDark
    },
    [danger]: {
        [mainColor]: 'red',
        [textColor]: 'white',
        [effectDirection]: toDark
    },
    [warning]: {
        [mainColor]: 'yellow',
        [textColor]: 'black',
        [effectDirection]: toLight
    },
    [info]: {
        [mainColor]: 'sky',
        [textColor]: 'black',
        [effectDirection]: toLight
    },
    [light]: {
        [mainColor]: 'zinc',
        [textColor]: 'black',
        [effectDirection]: toDark
    },
    [dark]: {
        [mainColor]: 'slate',
        [textColor]: 'white',
        [effectDirection]: toLight
    },
    [slate]: {
        [mainColor]: 'slate',
        [textColor]: 'white',
        [effectDirection]: toLight
    },
    [orange]: {
        [mainColor]: 'orange',
        [textColor]: 'white',
        [effectDirection]: toLight
    },
    [stone]: {
        [mainColor]: 'stone',
        [textColor]: 'white',
        [effectDirection]: toLight
    },
    [indigo]: {
        [mainColor]: 'indigo',
        [textColor]: 'white',
        [effectDirection]: toLight
    },
    [plug]: {
        [mainColor]: 'white',
        [textColor]: 'white',
        [effectDirection]: toLight
    }
}

const colorsList = [primary, secondary, success, danger, warning, info, light, dark, slate, orange, stone, indigo, plug]
const colorsListObj = {primary, secondary, success, danger, warning, info, light, dark, slate, orange, stone, indigo, plug}
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
    primary, secondary, success, danger, warning, info, light, dark, slate, orange, stone, indigo, plug,
    colorIndex, colorIndexLight, colorIndexOffset
}


export type IColorTypes =
    typeof primary
    | typeof secondary
    | typeof success
    | typeof danger
    | typeof warning
    | typeof info
    | typeof light
    | typeof dark
    | typeof slate
    | typeof orange
    | typeof stone
    | typeof indigo
    | typeof plug
