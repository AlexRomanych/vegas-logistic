// info Errors

const errors = [
    {
        path: '/:pathMatch(.*)*',
        alias: '/error/404',
        name: 'error.404',
        component: () => import('../views/errors/TheNotFound.vue')

    }
]


export default errors;
