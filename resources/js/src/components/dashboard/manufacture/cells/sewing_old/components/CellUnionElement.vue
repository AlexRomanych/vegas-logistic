<template>
    <div>
        <div class="flex justify-start items-center">

            <AppInputButton
                :id="element.element.size + element.element.model"
                :title="collapseChar"
                :type="labelType"
                height="h-[30px]"
                width="w-[30px]"
                @click="collapseLines"
            />

            <AppLabel
                :bold="true"
                :text="element.element.size"
                align="center"
                textSize="small"
                type="dark"
                width="w-[120px]"
                @click="collapseLines"
            />

            <AppLabel
                :bold="true"
                :text="element.element.model"
                align="left"
                textSize="small"
                type="dark"
                width="w-[150px]"
                @click="collapseLines"
            />

            <AppLabel
                :bold="true"
                :text="element.element.amount.toString()"
                align="center"
                textSize="small"
                type="dark"
                width="w-[40px]"
            />

            <AppLabel
                :bold="true"
                :text="(element.element.norm).toFixed(2)"
                align="center"
                textSize="small"
                type="primary"
                width="w-[60px]"
            />


        </div>

        <div v-if="!collapse">
            <div v-for="(order, idx) in element.orders" :key="idx">
                <CelUnionOrder
                    :order="order"
                />
            </div>
        </div>

    </div>

</template>

<script setup>

import {ref} from "vue"
import AppLabel from "/resources/js/src/components/ui/labels/AppLabel.vue"
import AppInputButton from "/resources/js/src/components/ui/inputs/AppInputButton.vue"
import CelUnionOrder
    from "/resources/js/src/components/dashboard/manufacture/cells/sewing/components/CellUnionOrder.vue"


const props = defineProps({
    element: {
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

// console.log(props.element)

</script>

<style scoped>

</style>
