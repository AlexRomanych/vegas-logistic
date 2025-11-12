// Этот helper используется для чтения upload файла

export async function getFileContent(file, contentType = 'text') {
    const reader = new FileReader()
    return new Promise((resolve, reject) => {
        reader.onload = () => resolve(reader.result);
        reader.onerror = reject;

        switch (contentType) {
            case 'text':
                reader.readAsText(file);
                break;
            case 'binary':
                reader.readAsArrayBuffer(file)
                break;
        }

        // if (contentType === 'text') {
        //     reader.readAsText(file);
        // } else if (contentType === 'binary') {
        //     reader.readAsArrayBuffer(file)
        // }

    })
}


/*
export async function getFileContent(
    file: File,
    contentType: 'text' | 'binary' = 'text'
): Promise<string | ArrayBuffer | null> {
    const reader = new FileReader()

    return new Promise((resolve, reject) => {
        reader.onload = () => resolve(reader.result)
        reader.onerror = () => reject(reader.error)

        switch (contentType) {
            case 'text':
                reader.readAsText(file)
                break
            case 'binary':
                reader.readAsArrayBuffer(file)
                break
            default:
                reject(new Error(`Unsupported content type: ${contentType}`))
        }
    })
}


 */
