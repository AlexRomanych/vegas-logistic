<template>

    <div class="flex items-center">

        <AppInputButton
            :id="item.date"
            :title="collapseChar"
            :type="'dark'"
            height="h-[30px]"
            width="w-[30px]"
            @click="collapseLines"
        />

        <AppLabel
            :bold="true"
            :text="formatDate(item.date) + ' (' + getDayOfWeek(item.date) + ')'"
            :type="getlabelType(item.date)"
            align="center"
            width="w-[150px]"
            @click="collapseLines"
        />

        <AppLabel
            :bold="true"
            :text="'∑ ' + getDayOrdersElementsAmount(item)"
            align="center"
            width="w-[100px]"
        />

        <AppLabel
            :bold="true"
            :text="'∑ ' + getDayOrdersTimesAmount(item).toFixed(2)"
            type="primary"
            align="center"
            width="w-[100px]"
        />

    </div>

    <div v-if="!collapse" class="ml-8">

        <div v-for="(element, idx) in item.union" :key="idx">

            <div>
                <CellUnionElement
                    :element="element"
                />
            </div>

        </div>

    </div>

</template>

<script setup>
import {ref} from "vue"
import CellUnionElement from '/resources/js/src/components/dashboard/manufacture/cells/sewing/components/CellUnionElement.vue'
import AppInputButton from "/resources/js/src/components/ui/inputs/AppInputButton.vue"
import AppLabel from "/resources/js/src/components/ui/labels/AppLabel.vue"

import {
    formatDate,
    getDayOfWeek,
    isWorkingDay,
    isToday
} from '/resources/js/src/app/helpers/helpers_date.js'

import {
    getDayOrdersElementsAmount,
    getDayOrdersTimesAmount,
} from '/resources/js/src/app/helpers/helpers_order.js'

const props = defineProps({
    item: {
        type: Object,
        required: true
    }
})

// console.log(props.item)

const collapse = ref(true)                  // указатель на схлопывание lines
const collapseChar = ref('+')               // символ схлопывания lines

// раскрашиваем дату
const getlabelType = (inDate) => {
    if (isToday(inDate)) return 'warning'

    if (isWorkingDay(inDate)) return 'dark'

    return 'danger'
}

const collapseLines = () => {
    collapse.value = !collapse.value
    collapseChar.value = collapse.value ? '+' : '-'
}

</script>

<style scoped>

</style>
