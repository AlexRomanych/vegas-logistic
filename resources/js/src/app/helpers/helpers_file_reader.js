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

