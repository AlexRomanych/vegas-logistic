import type { IRenderOrderLineModel } from '@/types'

export const MATTRESSES_TYPE = 'mattresses'
export const ACCESSORIES_TYPE = 'accessories'


// case MATTRESSES = 'mattresses';         // Заявка из матрасов
// case ACCESSORIES = 'accessories';       // Заявка из аксессуаров

export const RENDER_ORDER_LINE_MODEL_DRAFT: IRenderOrderLineModel = {
    base_composition: '',
    code_1c: '',
    is_auto: false,
    is_solid: false,
    is_solid_hard: false,
    is_solid_light: false,
    is_undefined: false,
    is_universal: false,
    kant: '',
    model_type: '',
    name_report: '',
    tkch: '',
}
