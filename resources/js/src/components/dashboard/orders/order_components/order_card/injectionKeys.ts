import { inject } from 'vue'
import type { InjectionKey, ComputedRef } from 'vue'
import type { IRenderOrder } from '@/types'

// __ order: IRenderOrder
export const OrderKey: InjectionKey<ComputedRef<IRenderOrder | null>> = Symbol('order')

// export function useOrder() {
//     const order = inject(OrderKey)
//     if (!order) throw new Error('Order not provided!')
//     return order
// }

export function useOrder() {
    const order = inject(OrderKey, null) // Указываем дефолтное значение null вторым аргументом
    if (!order) {
        console.error('OrderKey not found in provide chain')
        throw new Error('Order not provided!')
    }
    return order
}



// __ id: number
export const IdKey: InjectionKey<ComputedRef<number>> = Symbol('id')

export function useId() {
    const id = inject(IdKey)
    if (!id) throw new Error('Id not provided!')
    return id
}
