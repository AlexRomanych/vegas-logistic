import auth from "@/src/router/routes_auth"
import dashboard from "@/src/router/routes_dashboard"
import orders from "@/src/router/routes_orders"
import models from "@/src/router/routes_models"
import clients from "@/src/router/routes_clients"
import manufacture from "@/src/router/routes_manufacture"
import users from "@/src/router/routes_users"
import info from "@/src/router/routes_info"
import errors from "@/src/router/routes_errors"
import service from "@/src/router/routes_service"

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


    {
        path: '/plan',
        name: 'plan',
        component: () => import('@/src/components/dashboard/users/TheUsers.vue'),
    },


]

// console.log(routes)

export default routes
