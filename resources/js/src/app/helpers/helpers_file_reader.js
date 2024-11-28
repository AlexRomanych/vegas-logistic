// Этот helper используется для чтения upload файла

export async function getFileText(file) {
    const reader = new FileReader()
    return new Promise((resolve, reject) => {
        reader.onload = () => resolve(reader.result);
        reader.onerror = reject;
        reader.readAsText(file);
    })
}

