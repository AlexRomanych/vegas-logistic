// Здесь будут функци для проверки чего-либо

// Проверяет, является ли строка JSON
export function isJSON(str: string) {
    try {
        JSON.parse(str)
        return true
    } catch (e) {
        return false
    }
}

// Проверяет, является ли ответ с сервера ошибкой
export function isResponseWithError(response: any) {
    // return response['data']['status'] === 600
    return response?.data?.status === 600
}


// Descr: Проверка ответа API на кореектность
export function checkApiAnswer(response: any) {

    console.log('response: ', response)

    interface CheckResult {
        success: boolean,
        code: number,
        error: string | null,
        data: object | null | undefined | [] | string[],
        message: string | null | undefined | string[],
    }

    const checkResult: CheckResult = {
        success: false,
        code: 255,
        error: null,
        data: null,
        message: null
    }

    if (response.data === 'success') {
        checkResult.success = true
        checkResult.code = 0
    }

    return checkResult
}
