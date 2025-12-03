// INFO Константы для планов


import type { IPlan, IPlanLoad } from '@/types'

export const PLAN_DRAFT: IPlan = {
    id: 0,
    active: true,
    client: {
        id: 0,
        active: true,
        name: '',
        add_name: '',
        short_name: '',
        region: 'east'
    },
    order_no: '',
    period: '',
    order_type: {
        id: 0,
        color: '#000000',
        display_name: '',
        name: '',
    },
    load_position: 0,
    amounts: {
        averages: 0,
        children: 0,
        covers: 0,
        formats: 0,
        mattresses: 0,
        unknowns: 0,
        up_covers: 0,
        totals: 0,
    },
    load_at: '',
    action_at: '',
    load_at_previous: null,
    unload_at: null,
    status:  null,
    description: null,
    created_at: '',
    updated_at: null,
}


export const PLAN_LOAD_DRAFT: IPlanLoad = {
    id: 0,
    active: true,
    client: {
        id: 0,
        active: true,
        name: '',
        add_name: '',
        short_name: '',
        region: 'east'
    },
    order_no: '',
    period: '',
    order_type: {
        id: 0,
        color: '#000000',
        display_name: '',
        name: '',
    },
    load_position: 0,
    amounts: {
        averages: 0,
        children: 0,
        covers: 0,
        formats: 0,
        mattresses: 0,
        unknowns: 0,
        up_covers: 0,
        totals: 0,
    },
    load_at: '',
    action_at: '',
    load_at_previous: null,
    unload_at: null,
    status:  null,
    description: null,
    created_at: '',
    updated_at: null,
}
