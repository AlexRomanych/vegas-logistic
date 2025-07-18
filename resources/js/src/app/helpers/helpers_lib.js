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
export function isEmptyObj(obj) {

    if (typeof obj !== 'object' || obj === null) {
        return true

    }
    return Object.keys(obj).length === 0
}

// descr: Строку на числовую валидность
export function isNumeric(str) {
    if (typeof str !== 'string' || str.trim() === '') {
        return false;
    }
    return !isNaN(parseFloat(str)) && isFinite(Number(str));
}

// descr: Поверхностное клонирование объекта
export function cloneShallow(obj) {
    return JSON.parse(JSON.stringify(obj));
}

// descr: Глубокое клонирование объекта
export function cloneDeep(obj, hash = new WeakMap()) {
    if (obj === null || typeof obj !== 'object') {
        return obj; // Примитивы и null возвращаем как есть
    }

    if (hash.has(obj)) {
        return hash.get(obj); // Обнаружена циклическая ссылка, возвращаем ранее созданную копию
    }

    const clonedObj = Array.isArray(obj) ? [] : {};
    hash.set(obj, clonedObj); // Сохраняем ссылку на созданную копию для обнаружения циклов

    for (const key in obj) {
        if (Object.hasOwnProperty.call(obj, key)) {
            clonedObj[key] = deepClone(obj[key], hash);
        }
    }

    // Копируем Symbol-свойства (если необходимо)
    const symbols = Object.getOwnPropertySymbols(obj);
    for (const sym of symbols) {
        clonedObj[sym] = deepClone(obj[sym], hash);
    }

    // Копируем функции
    if (typeof obj === 'function') {
        return function (...args) {
            return obj.apply(this === clonedObj ? originalObject : this, args);
        };
    }

    return clonedObj;
}

const originalObjectWithMethod = {
    name: 'Alice',
    details: {age: 25},
    sayHello: function () {
        console.log(`Hello, ${this.name}!`);
    }
};

// descr: округление с точностью
export function round(number, precision = 0) {
    const factor = Math.pow(10, precision)
    return Math.round(number * factor) / factor
}


// ___ Глубокое копирование (только примитивы, без функций)
export function deepCopy(obj) {
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
        return copy
    }

    // Обработка обычных объектов
    if (typeof obj === 'object') {
        const copy = {};
        for (const key in obj) {
            // Проверяем, что свойство принадлежит самому объекту, а не унаследовано
            if (Object.prototype.hasOwnProperty.call(obj, key)) {
                //@ts-ignore
                copy[key] = deepCopy(obj[key]); // Рекурсивный вызов для каждого свойства объекта
            }
        }
        return copy;
    }

    // На всякий случай, если obj - это какой-то другой сложный тип, который мы не хотим копировать
    // (например, Date, RegExp, Map, Set, Function - хотя в данном случае функции исключены условием)
    // В этом сценарии, мы просто возвращаем сам объект, так как глубокое копирование для них не тривиально
    // и выходит за рамки "простых объектов без функций".
    return obj;
}
