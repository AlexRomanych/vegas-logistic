<template>
    <div v-for="item in cellsData" :key="item.date">


<!--        <div class="flex flex-row items-center">-->
<!--            <AppInputButton-->
<!--                :id="item.date"-->
<!--                :title="collapseChar"-->
<!--                :type="labelType"-->
<!--                height="h-[30px]"-->
<!--                width="w-[30px]"-->
<!--                @click="collapseLines"-->
<!--            />-->

<!--            <AppLabel-->
<!--                :bold="true"-->
<!--                :text="formattedDate(item.date)"-->
<!--                :type="labelType"-->
<!--                align="center"-->

<!--                width="w-[100px]"-->
<!--            />-->
<!--        </div>-->


        <div>
            <CellOrderBag
                :item="item"
            />
        </div>
    </div>
</template>

<script setup>
import {ref} from "vue";
import CellOrderBag from '/resources/js/src/components/dashboard/manufacture/cells/components/CellOrderBag.vue'


const props = defineProps({
    cellsData: {
        type: Object,
        required: true
    }


})



// const collapse = ref(true)                  // указатель на схлопывание lines
// const collapseChar = ref('+')               // символ схлопывания lines
//
// const labelType = ref('dark')
//
//
// const collapseLines = () => {
//     collapse.value = !collapse.value
//     collapseChar.value = collapse.value ? '+' : '-'
// }


</script>

<style scoped>

</style>
