// info Workers


const workers = [
    {
        path: '/workers',
        name: 'workers',
        component: () => import('/resources/js/src/components/dashboard/workers/TheWorkersShow.vue'),
        meta: {
            title: 'Список сотрудников',
        },
    },
    {
        path: '/workers/add',
        name: 'workers.add',
        component: () => import('/resources/js/src/components/dashboard/workers/TheWorkersShow.vue'),
        meta: {
            title: 'Добавление сотрудника',
        },
    }
]

// console.log(workers)
export default workers
