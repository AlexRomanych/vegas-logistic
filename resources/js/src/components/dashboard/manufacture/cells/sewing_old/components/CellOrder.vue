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
                textSize="small"
                @click="collapseLines"
            />

            <AppLabel
                :bold="true"
                :text="'№ ' + getPrettyOrderNumber(order.no_num)"
                :type="labelType"
                align="center"
                textSize="small"
                width="w-[100px]"
            />

            <AppLabel
                :bold="true"
                :text="'∑ ' + getOrderElementsAmount(order)"
                align="center"
                width="w-[70px]"
            />

            <AppLabel
                :bold="true"
                :text="'∑ ' + getOrderTimesAmount(order).toFixed(2)"
                type="primary"
                align="center"
                width="w-[100px]"
            />


        </div>


        <div v-if="!collapse">
            <div v-for="cell in order.cells" :key="cell.id">
                <CellLine
                    :cell="cell"
                />
            </div>
        </div>

    </div>

</template>

<script setup>
import {ref} from "vue"
import AppInputButton from '/resources/js/src/components/ui/inputs/AppInputButton.vue'
import AppLabel from "/resources/js/src/components/ui/labels/AppLabel.vue"
import CellLine from "/resources/js/src/components/dashboard/manufacture/cells/sewing/components/CellLine.vue"

import {
    getPrettyOrderNumber,
    getOrderElementsAmount,
    getOrderTimesAmount,
} from '/resources/js/src/app/helpers/helpers_order.js'


const props = defineProps({
    order: {
        type: Object,
        required: true
    }

})

const collapse = ref(true)                  // указатель на схлопывание lines
const collapseChar = ref('+')               // символ схлопывания lines
const labelType = ref('dark')

const collapseLines = () => {
    collapse.value = !collapse.value
    collapseChar.value = collapse.value ? '+' : '-'
}

</script>

<style scoped>

</style>
