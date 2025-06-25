// Info: Тут все общее

export const LINE_SEPARATOR = '&nl'  // new line - разделитель строк в тексте


// Descr: Проверка ответа API на кореектность
export function checkApiAnswer(response) {

    const checkResult = {
        success: true,
        code: 0,
        error: null,
        data: null,
        message: null
    }

    if (response == 'success') {
        checkResult.success = true
        checkResult.code = 0
    }

    return checkResult
}
