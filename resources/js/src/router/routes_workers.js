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
        component: () => import('/resources/js/src/components/dashboard/workers/TheWorkerEditForm.vue'),
        meta: {
            title: 'Добавление сотрудника',
            mode: 'add'
        },
    },
    {
        path: '/worker/:id',
        name: 'worker.show',
        component: () => import('/resources/js/src/components/dashboard/workers/TheWorkerEditForm.vue'),
        meta: {
            title: 'Редактирование сотрудника',
            mode: 'edit'
        },
    }
]

// console.log(workers)
export default workers
