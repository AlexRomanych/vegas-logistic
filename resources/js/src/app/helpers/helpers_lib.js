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

