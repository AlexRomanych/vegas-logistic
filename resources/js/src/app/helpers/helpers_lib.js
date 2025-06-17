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


