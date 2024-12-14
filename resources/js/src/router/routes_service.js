// info Service
// Тут все вспомогательные и проверочные маршруты

const service = [
    {
        path: '/ui.show',
        name: 'ui.show',
        component: () => import('@/src/views/TheComponentsExample.vue')
    }
]

export default service
