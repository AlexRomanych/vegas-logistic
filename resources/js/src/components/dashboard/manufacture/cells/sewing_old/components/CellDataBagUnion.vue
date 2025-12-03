<template>
    <div v-for="item in cellsDataUnion" :key="item.date">

        <div>
            <CellDataBagUnionDayBag
                :item="item"
            />
        </div>

    </div>
</template>

<script setup>
import {ref} from "vue"
import CellDataBagUnionDayBag from '/resources/js/src/components/dashboard/manufacture/cells/sewing/components/CellDataBagUnionDayBag.vue'


const props = defineProps({
    cellsData: {
        type: Object,
        required: true
    }
})

// attract Схлопываем все заявки в производственном дне
const cellsDataUnion = props.cellsData.map(manufDay => {

    // собираем все заявки в производственном дне в один массив
    const allElements = []
    manufDay.orders.forEach(order => {
        order.order.cells.forEach(cellElement => {
            allElements.push({

                element: {
                    size: cellElement.line.size,
                    model: cellElement.line.model,
                    amount: cellElement.amount,
                    norm: cellElement.norm,
                },

                order: {
                    // id: order.id,
                    short_name: order.order.client.short_name,
                    no_num: order.order.no_num,
                    amount: cellElement.amount,
                    norm: cellElement.norm,
                }

            })
        })
    })


    // обходим все собранные заявки в производственном дне и формируем схлопывание
    const union = []

    for (let i = 0; i < allElements.length; i++) {

        if (allElements[i].element.amount != 0) {

            const workElement = allElements[i].element
            const workElementOrders = [allElements[i].order]

            for (let j = i + 1; j < allElements.length; j++) {

                if (allElements[j].element.amount != 0) {

                    if (workElement.size === allElements[j].element.size && workElement.model === allElements[j].element.model) {

                        workElement.amount += allElements[j].element.amount
                        workElement.norm += allElements[j].element.norm
                        allElements[j].element.amount = 0
                        workElementOrders.push(allElements[j].order)
                    }
                }
            }

            union.push({element: workElement, orders: workElementOrders})
        }
    }

    union.sort((a, b) => a.element.model.localeCompare(b.element.model))    // сортируем по моделям

    // console.log(union)
    return {...manufDay, union}
})

// console.log(props.cellsData)
// console.log(cellsDataUnion)

</script>

<style scoped>

</style>
