<template>

    <div class="ml-2 mt-2">

        <div class="sticky top-0 flex pt-1 pb-1 bg-slate-100">

            <AppLabel
                text="Дата"
                type="primary"
                align="center"
                :width="dateColWidth"
            />

            <AppLabel
                text="Американец"
                type="primary"
                align="center"
                :width="machineColWidth"
            />

            <AppLabel
                text="Немец"
                type="primary"
                align="center"
                :width="machineColWidth"
            />

            <AppLabel
                text="Китаец"
                type="primary"
                align="center"
                :width="machineColWidth"
            />

            <AppLabel
                text="Кореец"
                type="primary"
                align="center"
                :width="machineColWidth"
            />

        </div>

        <div  class="flex-column">

            <AppLabel v-for="i in 100"
                text="lorem ipsum"
                align="center"
                width="w-[300px]"
            />

        </div>



    </div>


</template>

<script setup>
import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
import {ref} from 'vue'


// Получаем ширину ячейки в нужном формате
const getMachineColWidthCSS = (colWidth) => `w-[${colWidth}px]`

const machineColWidth = ref(getMachineColWidthCSS(300));        // под стегальную машину
const dateColWidth = ref(getMachineColWidthCSS(50));            // под дату

</script>

<style scoped>

</style>
