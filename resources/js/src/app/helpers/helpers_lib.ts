// Info Эдакая библиотека полезных функций

// descr: Возвращает только цифры из входящей строки
export function getStringPart(inStr = '') {
    if (typeof inStr !== 'string' || inStr === '') {
        return ''
    }

    return inStr.replace(/\d+/g, '')
}

// descr: Возвращает только буквы из входящей строки
export function getDigitPart(inStr = '') {
    if (typeof inStr !== 'string' || inStr === '') {
        return ''
    }

    return inStr.replace(/\D+/g, '')
}

// descr: Проверяет переменную на пустой объект
export function isEmptyObj(obj: object) {

    if (typeof obj !== 'object' || obj === null) {
        return true

    }
    return Object.keys(obj).length === 0
}

// descr: Строку на числовую валидность
export function isNumeric(str: string) {
    if (typeof str !== 'string' || str.trim() === '') {
        return false
    }
    return !isNaN(parseFloat(str)) && isFinite(Number(str))
}


// __ Проверка на число
export function isNumber(value: any): value is number {
    return typeof value === 'number' && !Number.isNaN(value);
}


// descr: Поверхностное клонирование объекта
export function cloneShallow(obj: object) {
    return JSON.parse(JSON.stringify(obj))
}

// ___ Глубокое клонирование объекта
export function cloneDeep<T>(obj: T, hash = new WeakMap()): T {

    // __ Примитивы и null возвращаем как есть
    if (obj === null || typeof obj !== 'object') {
        return obj
    }

    // __ Обнаружена циклическая ссылка, возвращаем ранее созданную копию
    if (hash.has(obj)) {
        return hash.get(obj) as T
    }

    // __ Правильно клонируем массивы и объекты
    const clonedObj: any = Array.isArray(obj) ? [] : {}
    hash.set(obj, clonedObj)       // Сохраняем ссылку на созданную копию для обнаружения циклов

    for (const key in obj) {
        if (Object.hasOwnProperty.call(obj, key)) {
            clonedObj[key] = cloneDeep((obj as any)[key], hash)
        }
    }

    // __ Копируем Symbol-свойства (если необходимо)
    const symbols = Object.getOwnPropertySymbols(obj)
    for (const sym of symbols) {
        clonedObj[sym] = cloneDeep((obj as any)[sym], hash)
    }

    // __ Копируем функции
    if (typeof obj === 'function') {
        return obj as T
        // return function (...args) {
        //     return obj.apply(this === clonedObj ? originalObject : this, args);
        // };
    }

    return clonedObj
}

// const originalObjectWithMethod = {
//     name: 'Alice',
//     details: {age: 25},
//     sayHello: function () {
//         console.log(`Hello, ${this.name}!`)
//     }
// }

// descr: округление с точностью
export function round(number: number, precision = 0) {
    const factor = Math.pow(10, precision)
    return Math.round(number * factor) / factor
}


// ___ Глубокое копирование (только примитивы, без функций)
export function deepCopy<T>(obj: T): T {
    // Обработка примитивных типов и null
    if (obj === null || typeof obj !== 'object') {
        return obj
    }

    // Обработка массивов
    if (Array.isArray(obj)) {
        const copy = []
        for (let i = 0; i < obj.length; i++) {
            copy[i] = deepCopy(obj[i]) // Рекурсивный вызов для каждого элемента массива
        }
        return copy as T
    }

    // Обработка обычных объектов
    if (typeof obj === 'object') {
        const copy = {}
        for (const key in obj) {
            // Проверяем, что свойство принадлежит самому объекту, а не унаследовано
            if (Object.prototype.hasOwnProperty.call(obj, key)) {
                //@ts-ignore
                copy[key] = deepCopy(obj[key]) // Рекурсивный вызов для каждого свойства объекта
            }
        }
        return copy as T
    }

    // На всякий случай, если obj - это какой-то другой сложный тип, который мы не хотим копировать
    // (например, Date, RegExp, Map, Set, Function - хотя в данном случае функции исключены условием)
    // В этом сценарии, мы просто возвращаем сам объект, так как глубокое копирование для них не тривиально
    // и выходит за рамки "простых объектов без функций".
    return obj
}
