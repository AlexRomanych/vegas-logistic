// Info: Тут все общее

// ___ Показывать ли ошибки catch, или нет
export const DISPLAY_CATCH_ERRORS = true

export const LINE_SEPARATOR = '&nl'  // new line - разделитель строк в тексте


// ___ Настройки лоадера
export const LOADER_SETTINGS = {
    // active: true,
    isFullPage: true,
    loader: 'dots', // 'spinner' | 'bars' | 'dots'
    zIndex: 9999,
    height: 64,
    width: 64,
    backgroundColor: '#444',
    color: 'blue',
    opacity: 0.5,
    canCancel: false,
}
