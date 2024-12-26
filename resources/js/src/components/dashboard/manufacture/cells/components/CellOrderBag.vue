<template>

    <div class="flex items-center">

        <AppInputButton
            :id="item.date"
            :title="collapseChar"
            :type="labelType"
            height="h-[30px]"
            width="w-[30px]"
            @click="collapseLines"
        />

        <AppLabel
            :bold="true"
            :text="formattedDate(item.date)"
            :type="labelType"
            align="center"
            width="w-[100px]"
        />
    </div>

    <div v-if="!collapse" class="ml-8">

        <div v-for="order in item.orders" :key="order.order.id">

            <div>
                <CellOrder
                    :order="order.order"
                />

            </div>

        </div>
    </div>

</template>

<script setup>
import CellOrder from '/resources/js/src/components/dashboard/manufacture/cells/components/CellOrder.vue'
import AppInputButton from "/resources/js/src/components/ui/inputs/AppInputButton.vue";
import AppLabel from "/resources/js/src/components/ui/labels/AppLabel.vue";

import {ref} from "vue";
// import CellLine from '/resources/js/src/components/dashboard/manufacture/cells/components/CellLine.vue'


const props = defineProps({
    item: {
        type: Object,
        required: true
    }
})

console.log('item', props.item)

const collapse = ref(true)                  // указатель на схлопывание lines
const collapseChar = ref('+')               // символ схлопывания lines

const labelType = ref('dark')


const collapseLines = () => {
    collapse.value = !collapse.value
    collapseChar.value = collapse.value ? '+' : '-'
}

const formattedDate = (date) => {
    return new Date(date).toLocaleDateString()
}

// console.log(props.orders)


</script>

<style scoped>

</style>
