// Info Константы для работы с Блоками (Blocks)

import type { IBlockCollection } from '@/types'

export const LINE_0 = 1
export const LINE_1 = 1
export const LINE_2 = 2

export const UNIT        = ''
export const UNIT_PICS   = 'шт.'
export const UNIT_METERS = 'м.п.'


// __ Объект Коллекции блоков
export const BLOCK_COLLECTION_DRAFT: IBlockCollection = {
    id          : 0,
    code_1c     : '',
    name        : '',
    unit        : null,
    kdb         : null,
    line        : LINE_0,
    priority    : 0,
    height      : 0,
    length      : 0,
    productivity: 0,
    active      : true,
    own         : true,
    description : null,
    blocks      : [],
}
