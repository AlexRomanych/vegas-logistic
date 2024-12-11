
// todo Сделать API на fetch без axios
fetch('/api/v1/orders/upload', {
    method: 'POST',
    headers: {
        // 'Content-Type': 'application/x-www-form-urlencoded',
        'Content-Type': 'multipart/form-data',
        // 'Content-Type': 'application/json',
        // 'Content-Type': 'multipart/form-data; charset=utf-8; boundary=' + Math.random().toString().substr(2),
        'Authorization': `Bearer ${localStorage.getItem('token')}`
    },
    body: fileData
})
    .then(response => console.log(response.data))
    .catch(error => {
        debugger
        console.error('Error:', error);
    });
