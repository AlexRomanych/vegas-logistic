import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const getRouter = () => ({ route, router })
export default getRouter
