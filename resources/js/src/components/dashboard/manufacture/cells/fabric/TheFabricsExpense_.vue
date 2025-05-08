<template>

    <div class="ml-2 mt-2">

        <div class="sticky top-0 flex pt-1 pb-1 bg-blue-200 border-2 rounded-lg border-blue-700 p-1 mb-1 max-w-fit">


            <AppLabel
                :width="'w-[250px]'"
                align="center"
                class="border-2 rounded-lg border-blue-700"
                text="Полотно стеганное"
                type="primary"
            />

            <AppLabel
                :width="'w-[60px]'"
                align="center"
                class="border-2 rounded-lg border-blue-700"
                text="Буф."
                type="primary"
            />

            <AppLabel
                :width="'w-[60px]'"
                align="center"
                class="border-2 rounded-lg border-blue-700"
                text="Расх."
                type="primary"
            />

            <AppLabel
                :width="'w-[60px]'"
                align="center"
                class="border-2 rounded-lg border-blue-700"
                text="Δ"
                type="primary"
            />

            <AppLabel
                :width="'w-[60px]'"
                align="center"
                class="border-2 rounded-lg border-blue-700"
                text="СЗ"
                type="primary"
            />

            <AppLabel
                :width="'w-[800px]'"
                align="center"
                class="border-2 rounded-lg border-blue-700"
                text="Заявки"
                type="primary"
            />

        </div>

        <!-- Заглушка -->
        <div class="flex">

            <AppLabel
                :width="'w-[512px]'"
                height="h-[50px]"
                align="center"
                class="border-2 rounded-lg border-blue-700"
                text="Заявки:"
                type="info"
            />


            <AppLabel
                v-for="x in 30"
                :width="'w-[70px]'"
                height="h-[50px]"
                align="center"
                class="border-2 rounded-lg border-blue-700"
                text="ЛММ_Минск №289 01.04.25"
                textSize="micro"
                title="Всплывающая подсказка"
                type="warning"
            />

        </div>

        <div v-for="x in 20">

            <div class="flex">

                <AppLabel
                    :width="'w-[255px]'"
                    align="left"
                    class="cursor-pointer"
                    text="ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)"
                    textSize="micro"
                    title="Всплывающая подсказка"
                />

                <AppLabel
                    :text="(Math.random() * (500) + 1).toFixed(1)"
                    :width="'w-[60px]'"
                    align="center"
                    textSize="micro"
                    title="Всплывающая подсказка"
                />

                <AppLabel
                    :text="(Math.random() * (500) + 1).toFixed(1)"
                    :width="'w-[60px]'"
                    align="center"
                    textSize="micro"
                    title="Всплывающая подсказка"
                />

                <AppLabel
                    :text="(Math.random() * (500) + 1).toFixed(1)"
                    :type="Math.floor(Math.random() * 10 + 1) % 2 == 0 ? 'success' : 'danger'"
                    :width="'w-[60px]'"
                    align="center"
                    textSize="micro"
                    title="Всплывающая подсказка"
                />

                <AppLabel
                    :width="'w-[60px]'"
                    align="center"
                    text="СЗ"
                    textSize="micro"
                    type="warning"
                />

            </div>

        </div>


    </div>


</template>

<script setup>
import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
import {ref, watch} from 'vue'


// Получаем ширину ячейки в нужном формате
const getMachineColWidthCSS = (colWidth) => `w-[${colWidth}px]`

const machineColWidth = ref(getMachineColWidthCSS(378))         // 378 под стегальную машину (указываем динамический стиль в <style> для загрузки)

const dateColWidth = ref(getMachineColWidthCSS(48))             // под дату
const leftDateColWidth = ref(getMachineColWidthCSS(50))         // под дату слева
const actionsColWidth = ref(getMachineColWidthCSS(150))          // под действия

const fabricColWidth = ref(getMachineColWidthCSS(250))          // название ПС
const amountRollsColWidth = ref(getMachineColWidthCSS(30))      // кол-во в рулонах
const amountMetersColWidth = ref(getMachineColWidthCSS(40))     // кол-во в погонных метрах
const laborsColWidth = ref(getMachineColWidthCSS(30))           // трудозатраты


</script>

<style scoped>

.load-css {

    @apply
    w-[330px]
    w-[20px]
    w-[30px]
    w-[48px]
    w-[51px]
    w-[50px]
    w-[450px]
    w-[500px]
    w-[600px]
    w-[378px]
    w-[80px]
}

</style>
