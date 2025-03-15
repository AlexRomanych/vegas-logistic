// info Autorization

const auth = [
    {
        path: '/login',
        alias: '/',
        name: 'login',
        component: () => import('/resources/js/src/views/auth/TheLogin.vue'),
    },

// {
//     path: '/login',
//     name: 'login',
//     component: () => import('/resources/js/src/views/auth/TheLogin.vue'),
// },


    {
        path: '/register',
        name: 'register',
        component: () => import('/resources/js/src/views/auth/TheRegister.vue'),
    },

]

export default auth
