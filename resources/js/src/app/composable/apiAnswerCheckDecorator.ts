// ___ Простая обертка поверх ответа от сервера
// ___ Потом будет использоваться для проверки ответа от сервера
// TODO: Переписать обработчик ошибок
export function apiAnswerCheckDecorator<T>(data: T): T {
    return data
}
