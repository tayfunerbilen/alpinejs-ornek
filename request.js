export default async function request(action, data = {}) {
    const formData = new FormData()
    formData.append('action', action)
    for ( [key, value] of Object.entries(data)) {
        formData.append(key, value)
    }
    const result = await fetch('http://localhost/php-backend/', {
        method: 'POST',
        body: formData
    })
    return result.json()
}