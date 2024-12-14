// Здесь будут функци для проверки чего-либо

// Проверяет, является ли строка JSON
export function isJSON(str) {
    try {
        JSON.parse(str)
        return true
    } catch (e) {
        return false
    }
}

// Проверяет, является ли ответ с сервера ошибкой
export function isResponseWithError(response) {
    // return response['data']['status'] === 600
    return response?.data?.status === 600
}
