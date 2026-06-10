// Info Константы для работы с Блоками (Blocks)

import type { IBlockCollection, IBlock } from '@/types'

export const LINE_0 = 0
export const LINE_1 = 1
export const LINE_2 = 2

export const LINE_0_NAME = 'Н/Д'
export const LINE_1_NAME = 'Линия 1'
export const LINE_2_NAME = 'Линия 2'

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
    kdb_id      : null,
    line        : LINE_1,
    line_alt    : LINE_0,
    priority    : 0,
    height      : 0,
    length      : 0,
    productivity: 0,
    active      : true,
    own         : true,
    description : null,
    blocks      : [],
}


// __ Объект Блока
export const BLOCK_DRAFT: IBlock = {
    id         : 0,
    code_1c    : '',
    name       : '',
    unit       : null,
    width      : 0,
    length     : 0,
    active     : true,
    description: null,
    collection: '000000000' // Без коллекции

}
