import type { IAssemblyModelManufactureGroup } from '@/types'


// __ Болванка Группы для Сортировки
export const ASSEMBLY_MODEL_MANUFACTURE_GROUP_DRAFT = {
    id          : 0,
    name        : '',
    active      : true,
    group_number: 0,
    color       : '',
    description : null,
} as const satisfies IAssemblyModelManufactureGroup
