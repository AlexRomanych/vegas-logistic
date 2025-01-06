<template>


    <div>

        <div class="flex items-center">
            <AppInputButton
                :id="order.id"
                :title="collapseChar"
                :type="labelType"
                height="h-[30px]"
                width="w-[30px]"
                @click="collapseLines"
            />

            <AppLabel
                :bold="true"
                :text="order.client.short_name"
                :type="labelType"
                @click="collapseLines"
            />

            <AppLabel
                :bold="true"
                :text="'№ ' + orderNo"
                :type="labelType"
                align="center"
                width="w-[100px]"
            />

            <AppLabel
                :bold="true"
                :text="'∑ ' + totalAmount"
                :type="labelType"
                align="center"
                width="w-[100px]"
            />

            <AppLabel
                :bold="true"
                :text="formattedDate(order.load_date)"
                :type="labelType"
                align="center"
                width="w-[100px]"
            />

            <div class="cursor-pointer">
                <AppLabel
                    :text="orderDeletedText"
                    :type="labelType"
                    align="center"
                    width="w-[100px]"
                    @click="deleteOrder"
                />
            </div>

        </div>

        <div v-if="order.lines.length > 0 && !collapse"
             class="ml-10">

            <table>
                <tbody>
                <TheOrderLine
                    v-for="line in order.lines"
                    :key="line.id"
                    :line="line"
                />
                </tbody>
            </table>

        </div>

    </div>
</template>

<script setup>
import {ref, computed} from 'vue'


import TheOrderLine from '@/src/components/dashboard/orders/components/TheOrderLine.vue'
import AppInputButton from '@/src/components/ui/inputs/AppInputButton.vue'
import AppLabel from "@/src/components/ui/labels/AppLabel.vue"

const props = defineProps({
    order: {
        type: Object,
        required: true
    },
})

const emits = defineEmits(['deleteOrder'])

// Обрабатываем красивый вывод номера заказа
let orderNo = props.order.no_num
if (orderNo.indexOf('.') !== -1) {

    const orderNoDec = orderNo.split('.')[0]
    const orderNoFloat = orderNo.split('.')[1]

    if (+orderNoFloat === 0) {
        orderNo = orderNoDec
    }
}

// Форматируем дату в определенный формат
const formattedDate = (date) => {
    return new Date(date).toLocaleDateString()
}

// Тут схлопывание lines
const collapse = ref(true)                  // указатель на схлопывание lines
const collapseChar = ref('+')               // символ схлопывания lines

const orderDeleted = ref(props.order.deleted)
const orderDeletedText = ref('Удалить')
// const labelType = computed(() => orderDeleted.value ? 'danger' : 'dark')
const labelType = ref('dark')


const collapseLines = () => {
    collapse.value = !collapse.value
    collapseChar.value = collapse.value ? '+' : '-'
}

// Удаление заказа
const deleteOrder = () => {
    emits('deleteOrder', props.order.id)
    orderDeleted.value = !orderDeleted.value
    // console.log(orderDeleted.value)
    orderDeletedText.value = !orderDeleted.value ? 'Удалить' : 'Отменить'
    labelType.value = orderDeleted.value ? 'danger' : 'dark'

}

// Тут подсчет общего количества элементов
const totalAmount = props.order.lines.reduce((acc, line) => acc + line.amount, 0)


// console.log(props.order)
</script>

<style scoped>
tbody > tr:nth-of-type(even) {
    /*   background-color: rgb(237 238 242); */
    @apply bg-slate-200
}
</style>
