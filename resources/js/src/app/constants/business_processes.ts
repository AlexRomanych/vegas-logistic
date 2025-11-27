import type { IBusinessProcessesConst, IBusinessProcessList } from '@/types'

// ___ Константы бизнес процессов
export const BUSINESS_PROCESSES: IBusinessProcessesConst = {
    ORDER_MOVING: {
        id: 1,
        name: 'Схема движения заявки'
    }
}


export const BUSINESS_PROCECC_DRAFT: IBusinessProcessList = {
    id: 0,
    name: '',
    active: true,
    description: null,
    comment: null,
    belongs_to: null,
    type: '',
    reference_node: null,
    start_node: null,
    finish_node: null,
}
