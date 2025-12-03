import type {
    IBusinessProcessesConst,
    IBusinessProcessList,
    IBusinessProcessNode,
    IBusinessProcessNodesConst
} from '@/types'

// ___ Константы бизнес процессов
export const BUSINESS_PROCESSES: IBusinessProcessesConst = {
    DEFAULT: {
        ID: 0,
        NAME: 'Дефолтный БП'
    },
    ORDER_MOVING: {
        ID: 1,
        NAME: 'Схема движения заявки'
    }
}


// ___ Константы узлов бизнес процесса
export const BUSINESS_PROCESS_NODES: IBusinessProcessNodesConst = {
    DEFAULT: {
        ID: 0,
        NAME: 'Дефолтный узел'
    },
    LOADS: {
        ID: 12,
        NAME: 'Загрузка на складе Вегас'
    },
    CUTTING: {
        ID: 7,
        NAME: 'Раскрой'
    },
    SEWING: {
        ID: 8,
        NAME: 'Пошив'
    },
    ASSEMBLY: {
        ID: 9,
        NAME: 'Сборка'
    },
}




// ___ Драфт бизнес процесса
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


// ___ Драфт узла бизнес-процесса
export const BUSINESS_PROCESS_NODE_DRAFT: IBusinessProcessNode = {
    id: 0,
    name: '',
    type: null,
    has_module: null,
    route_name: null,
    allow_action: null,
    active: false,
    status: null,
    order: 0,
    description: null
}
