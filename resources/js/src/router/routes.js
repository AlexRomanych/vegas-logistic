import auth from "/resources/js/src/router/routes_auth"
import dashboard from "/resources/js/src/router/routes_dashboard"
import orders from "/resources/js/src/router/routes_orders"
import models from "/resources/js/src/router/routes_models"
import clients from "/resources/js/src/router/routes_clients"
import manufacture from "/resources/js/src/router/routes_manufacture"
import users from "/resources/js/src/router/routes_users"
import info from "/resources/js/src/router/routes_info"
import errors from "/resources/js/src/router/routes_errors"
import service from "/resources/js/src/router/routes_service"
import workers from "/resources/js/src/router/routes_workers.js"

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


    {
        path: '/plan',
        name: 'plan',
        component: () => import('@/src/components/dashboard/users/TheUsers.vue'),
    },


]



// console.log(routes)

export default routes
