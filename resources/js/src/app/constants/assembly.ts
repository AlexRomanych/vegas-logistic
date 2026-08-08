import type { IAssemblyModelManufactureGroup, IAssemblySector, IAssemblySectorKeys, IAssemblyTaskChange, IAssemblyTaskChangeKeys, IColorTypes } from '@/types'

// --- --------------------------------------------------------------------
// --- -------------------- Константы Смен СЗ -----------------------------
// --- --------------------------------------------------------------------
// __ Смены
export const CHANGE_1 = '1'
export const CHANGE_2 = '2'

// __ Режим Сменной Работы
export const CHANGE_MODE = CHANGE_1 satisfies IAssemblyTaskChangeKeys

// __ Константы смен
export const CHANGES = {
    CHANGE_1: {
        ID        : 1,
        NAME      : CHANGE_1,
        TITLE     : '1',
        ICON      : '①',
        TITLE_ROME: 'I',
        TYPE      : 'indigo',
        TIME      : '08:30-20:30',

    },
    CHANGE_2: {
        ID        : 2,
        NAME      : CHANGE_2,
        TITLE     : '2',
        ICON      : '②',
        TITLE_ROME: 'II',
        TYPE      : 'orange',
        TIME      : '20:30-08:30',
    },

} as const satisfies Record<string, IAssemblyTaskChange>


// __ Болванка Группы для Сортировки
export const ASSEMBLY_MODEL_MANUFACTURE_GROUP_DRAFT = {
    id          : 0,
    name        : '',
    active      : true,
    group_number: 0,
    color       : '',
    description : null,
} as const satisfies IAssemblyModelManufactureGroup


// --- --------------------------------------------------------------------
// --- ---------------- Константы Участков (SECTORS)  ---------------------
// --- --------------------------------------------------------------------
export const ASSEMBLY_TASK_SECTOR_COCONUT    = 'coconut'          // __ Кокос
export const ASSEMBLY_TASK_SECTOR_LATEX      = 'latex'              // __ Латекс
export const ASSEMBLY_TASK_SECTOR_LAYER      = 'layer'              // __ Тонкий настил
export const ASSEMBLY_TASK_SECTOR_FOAM_LAYER = 'foam_layer'    // __ Настилы
export const ASSEMBLY_TASK_SECTOR_FOAM_SIDE  = 'foam_side'      // __ Борта
export const ASSEMBLY_TASK_SECTOR_LAMIT      = 'lamit'              // __ Ламит
export const ASSEMBLY_TASK_SECTOR_TABLE      = 'table'              // __ Стол


export const ASSEMBLY_SECTORS = {
    [ASSEMBLY_TASK_SECTOR_COCONUT]: {
        ID   : 1,
        NAME : ASSEMBLY_TASK_SECTOR_COCONUT,
        TITLE: 'Кокос',
        ICON : '',
        TYPE : 'primary',
    },
    [ASSEMBLY_TASK_SECTOR_LATEX]: {
        ID   : 2,
        NAME : ASSEMBLY_TASK_SECTOR_LATEX,
        TITLE: 'Латекс',
        ICON : '',
        TYPE : 'success',
    },
    [ASSEMBLY_TASK_SECTOR_LAYER]: {
        ID   : 3,
        NAME : ASSEMBLY_TASK_SECTOR_LAYER,
        TITLE: 'Тонкий настил',
        ICON : '',
        TYPE : 'dark',
    },
    [ASSEMBLY_TASK_SECTOR_FOAM_LAYER]: {
        ID   : 4,
        NAME : ASSEMBLY_TASK_SECTOR_FOAM_LAYER,
        TITLE: 'ППУ Настилы',
        ICON : '',
        TYPE : 'dark',
    },
    [ASSEMBLY_TASK_SECTOR_FOAM_SIDE] : {
        ID   : 5,
        NAME : ASSEMBLY_TASK_SECTOR_FOAM_SIDE,
        TITLE: 'ППУ Борта',
        ICON : '',
        TYPE : 'dark',
    }
} as const satisfies Record<string, IAssemblySector>

