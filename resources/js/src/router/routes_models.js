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
]

export default models
