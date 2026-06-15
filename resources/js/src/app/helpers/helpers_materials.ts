import type { IMaterialRenderGroup } from '@/types'


// __ Подготавливаем входящий массив для Материалов для отображения
export function sortMaterialsOrdersData(data: IMaterialRenderGroup[]): IMaterialRenderGroup[] {
    let materialsGroups = data
    materialsGroups     = materialsGroups
        .map(group => {
            return {
                ...group,
                collapsed : true,
                categories: group.categories
                    .map(category => {
                        return {
                            ...category,
                            collapsed: true,
                            materials: category.materials
                                .map(material => {
                                    return {
                                        ...material,
                                        collapsed: true,
                                        orders   : material.orders
                                            .map(order => {
                                                return {
                                                    ...order,
                                                    collapsed   : true,
                                                    order_lines : order.order_lines.toSorted((a, b) => a.model_name.localeCompare(b.model_name)),
                                                    order_amount: order.order_lines.reduce((acc, line) => acc + line.amount, 0),
                                                    order_total : order.order_lines.reduce((acc, line) => acc + line.total, 0),
                                                }
                                            })
                                            .toSorted((a, b) => a.client_name.localeCompare(b.client_name)),
                                        // material_total: material.orders.reduce((acc, order) => acc + order.order_total, 0),
                                    }
                                })
                                .map(material => {
                                    return {
                                        ...material,
                                        material_amount: material.orders.reduce((acc, order) => acc + order.order_amount, 0),
                                        material_total : material.orders.reduce((acc, order) => acc + order.order_total, 0),
                                    }
                                })
                                .toSorted((a, b) => a.name.localeCompare(b.name)),
                        }
                    })
                    .toSorted((a, b) => a.name.localeCompare(b.name)),
            }
        })
        .toSorted((a, b) => a.name.localeCompare(b.name))
    return materialsGroups
}
