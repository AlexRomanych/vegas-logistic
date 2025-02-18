// info Service
// Тут все вспомогательные и проверочные маршруты

const service = [
    {
        path: '/ui.show',
        name: 'ui.show',
        component: () => import('/resources/js/src/views/TheComponentsExample.vue')
    }
]

export default service
