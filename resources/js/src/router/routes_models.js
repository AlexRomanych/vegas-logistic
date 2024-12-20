// info Models


const models = [
// {
//     path: '/models',
//     name: 'models',
//     component: () => import('@/src/components/dashboard/TheDashboard.vue'),
//     meta: {source: 'TheModels'},
// },
    {
        path: '/models',
        name: 'models',
        component: () => import('@/src/components/dashboard/models/TheModels.vue'),
        meta: {destination: '/models'},
    },
    {
        path: '/models/load',
        name: 'models.load',
        component: () => import('@/src/components/dashboard/models/TheModelsLoad.vue'),
    },
]

export default models
