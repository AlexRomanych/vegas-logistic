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
        path: '/worker/edit/:id',
        name: 'worker.edit',
        component: () => import('/resources/js/src/components/dashboard/workers/TheWorkerEditForm.vue'),
        meta: {
            title: 'Редактирование сотрудника',
            mode: 'edit'
        },
    }
]

// console.log(workers)
export default workers
