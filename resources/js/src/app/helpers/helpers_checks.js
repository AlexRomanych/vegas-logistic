// Здесь будут функци для проверки чего-либо

export function isJSON(str) {
    try {
        JSON.parse(str)
        return true
    } catch (e) {
        return false
    }
}
