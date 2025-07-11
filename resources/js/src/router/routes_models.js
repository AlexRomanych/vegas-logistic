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
        component: () => import('@/components/dashboard/models/TheModelsShow.vue'),
        meta: {
            title: 'Модели - справочник',
            destination: '/models',
        },
    },
    {
        path: '/models/load',
        name: 'models.load',
        component: () => import('@/components/dashboard/models/TheModelsLoad.vue'),
    },
]

export default models
