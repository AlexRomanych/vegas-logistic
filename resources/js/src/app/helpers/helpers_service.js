// Здесь будут храниться функции, которые для обслуживания приложения

export function openNewTab(htmlContent) {
    // Создаем новый объект window
    const newWindow = window.open();
    // Получаем доступ к document нового окна
    const newDoc = newWindow.document;
    // Записываем HTML в новый документ
    newDoc.write(htmlContent);
    newDoc.close();
}
