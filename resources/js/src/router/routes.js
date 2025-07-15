import auth from '@/router/routes_auth'
import dashboard from '@/router/routes_dashboard'
import orders from '@/router/routes_orders'
import models from '@/router/routes_models'
import clients from '@/router/routes_clients'
import manufacture from '@/router/routes_manufacture'
import users from '@/router/routes_users'
import info from '@/router/routes_info'
import errors from '@/router/routes_errors'
import service from '@/router/routes_service'
import workers from '@/router/routes_workers.js'
import templates from '@/router/routes_templates.js'
import reasons from '@/router/routes_reasons.js'

const routes = [
    ...auth,
    ...dashboard,
    ...orders,
    ...models,
    ...clients,
    ...manufacture,
    ...users,
    ...info,
    ...errors,
    ...service,
    ...workers,
    ...templates,
    ...reasons,

    {
        path: '/plan',
        name: 'plan',
        component: () => import('@/components/dashboard/users/TheUsers.vue'),
    },

    {
        path: '/test/show',
        name: 'test.show',
        component: () => import('/resources/js/src/components/ui/test/TestModal.vue'),
    },
]

// console.log(routes)

export default routes
