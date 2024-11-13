import axios from 'axios';


const jwtAxios = axios.create({
    withCredentials: false,
    baseURL: '/api/v1'
})


export async function jwtGet(url) {
    // console.log(url)

    const res = await jwtAxios.get(url)
    const data = res.data
    // const data = await res
    // console.log(data)
    // console.log(data.models[2])
    return data
}

// export default jwtAxios
