// info Logs
import type { IRouteMeta } from '@/types'

const PREFIX = '/logs'

const logs = [
    {
        path: `${PREFIX}/fabrics/rolls/execute`,              // __ Выполнение физических рулонов (FabricTaskRolls)
        name: 'manufacture.logs.fabrics.rolls.execute',
        component: () => import('@/components/dashboard/manufacture/logs/TheFabricRollsExecuteLog.vue'),
        meta: {
            title: 'История выполнения рулонов',
            // mode: FABRIC_PAGE_MODE.EDIT,
        } as IRouteMeta,
    },

]

export default logs
