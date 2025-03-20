<template>

    <div class="ml-2 mt-2">

        <div class="sticky top-0 flex pt-1 pb-1 bg-blue-200 border-2 rounded-lg border-blue-700 p-1 mb-1 max-w-fit">


            <AppLabelMultiline
                :text="['№', 'рул.']"
                :width="'w-[60px]'"
                align="center"
                class="border-2 rounded-lg border-blue-700"
                type="primary"
            />

            <AppLabelMultiline
                :text="['Полотно', 'стеганное']"
                :width="'w-[250px]'"
                align="center"
                class="border-2 rounded-lg border-blue-700"
                type="primary"
            />

            <AppLabelMultiline
                :text="['Ткань', 'м.п.']"
                :width="'w-[70px]'"
                align="center"
                class="border-2 rounded-lg border-blue-700"
                type="primary"
            />

            <AppLabelMultiline
                :text="['ПС', 'м.п.']"
                :width="'w-[70px]'"
                align="center"
                class="border-2 rounded-lg border-blue-700"
                type="primary"
            />

            <AppLabelMultiline
                :text="['Дата', 'пр-ва']"
                :width="'w-[100px]'"
                align="center"
                class="border-2 rounded-lg border-blue-700"
                type="primary"
            />

            <AppLabelMultiline
                :text="['Ответственный за', 'производство']"
                :width="'w-[150px]'"
                align="center"
                class="border-2 rounded-lg border-blue-700"
            />

            <AppLabelMultiline
                :text="['Учет', 'в 1С']"
                :width="'w-[60px]'"
                align="center"
                class="border-2 rounded-lg border-blue-700"
                type="primary"
            />

            <AppLabelMultiline
                :text="['Дата', 'учета в 1С']"
                :width="'w-[100px]'"
                align="center"
                class="border-2 rounded-lg border-blue-700"
                type="primary"
            />

            <AppLabelMultiline
                :text="['Ответственный за', 'учет в 1С']"
                :width="'w-[150px]'"
                align="center"
                class="border-2 rounded-lg border-blue-700"
            />

            <AppLabelMultiline
                :text="['--->', 'закрой']"
                :width="'w-[60px]'"
                align="center"
                class="border-2 rounded-lg border-blue-700"
                type="primary"
            />

            <AppLabelMultiline
                :text="['Дата', '--->']"
                :width="'w-[100px]'"
                align="center"
                class="border-2 rounded-lg border-blue-700"
                type="primary"
            />

            <AppLabelMultiline
                :text="['Ответственный за', '--->']"
                :width="'w-[150px]'"
                align="center"
                class="border-2 rounded-lg border-blue-700"
            />

            <AppLabelMultiline
                :text="['Примечание', '']"
                :width="'w-[400px]'"
                align="center"
                class="border-2 rounded-lg border-blue-700"
                type="primary"
            />


        </div>


        <!--        &lt;!&ndash; Заглушка &ndash;&gt;-->
        <!--        <div class="flex">-->

        <!--            -->
        <!--            <AppLabel-->
        <!--                :width="'w-[512px]'"-->
        <!--                height="h-[50px]"-->
        <!--                align="center"-->
        <!--                class="border-2 rounded-lg border-blue-700"-->
        <!--                text="Заявки:"-->
        <!--                type="info"-->
        <!--            />-->


        <!--            <AppLabel-->
        <!--                v-for="x in 11"-->
        <!--                :width="'w-[70px]'"-->
        <!--                height="h-[50px]"-->
        <!--                align="center"-->
        <!--                class="border-2 rounded-lg border-blue-700"-->
        <!--                text="ЛММ_Минск №289 01.04.25"-->
        <!--                textSize="micro"-->
        <!--                title="Всплывающая подсказка"-->
        <!--                type="warning"-->
        <!--            />-->

        <!--        </div>-->

        <div class="ml-1">

            <div v-for="x in 40">

                <div class="flex">

                    <AppLabel
                        :width="'w-[62px]'"
                        align="center"
                        text="0000000"
                        textSize="micro"
                        title="Номер рулона"
                    />

                    <AppLabel
                        :width="'w-[250px]'"
                        align="left"
                        class="cursor-pointer"
                        text="ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)"
                        textSize="micro"
                        title="Всплывающая подсказка"
                    />

                    <AppLabel
                        :text="(Math.random() * (500) + 1).toFixed(1)"
                        :width="'w-[70px]'"
                        align="center"
                        textSize="micro"
                        title="Всплывающая подсказка"
                    />

                    <AppLabel
                        :text="(Math.random() * (500) + 1).toFixed(1)"
                        :width="'w-[70px]'"
                        align="center"
                        textSize="micro"
                        title="Всплывающая подсказка"
                    />

                    <AppLabel
                        :width="'w-[100px]'"
                        align="center"
                        class="cursor-pointer"
                        text="00.00.0000 00:00"
                        textSize="micro"
                        title="Всплывающая подсказка"
                    />

                    <AppLabel
                        :width="'w-[150px]'"
                        align="center"
                        class="cursor-pointer"
                        text="Конончик М.Н."
                        textSize="micro"
                        title="Всплывающая подсказка"
                    />

                    <!--                    <AppCheckbox-->
                    <!--                        :checkboxData="checkboxData"-->
                    <!--                        dir="horizontal"-->
                    <!--                        inputType="radio"-->
                    <!--                        legend="Many Numbers"-->
                    <!--                        type="secondary"-->
                    <!--                        width="w-[600px]"-->
                    <!--                        @checked="checkedHandler"-->
                    <!--                    />-->

<!--                    <AppCheckbox-->
<!--                        :checkboxData="checkboxData"-->
<!--                        dir="horizontal"-->
<!--                        width="w-[60px]"-->
<!--                    />-->

                    <AppCheckboxSimple
                        :width="'w-[64px]'"
                    />

                    <AppLabel
                        :width="'w-[100px]'"
                        align="center"
                        class="cursor-pointer"
                        text="00.00.0000 00:00"
                        textSize="micro"
                        title="Всплывающая подсказка"
                    />

                    <AppLabel
                        :width="'w-[150px]'"
                        align="center"
                        class="cursor-pointer"
                        text="Конончик М.Н."
                        textSize="micro"
                        title="Всплывающая подсказка"
                    />

                    <AppCheckboxSimple
                        :width="'w-[64px]'"
                    />

                    <AppLabel
                        :width="'w-[100px]'"
                        align="center"
                        class="cursor-pointer"
                        text="00.00.0000 00:00"
                        textSize="micro"
                        title="Всплывающая подсказка"
                    />

                    <AppLabel
                        :width="'w-[150px]'"
                        align="center"
                        class="cursor-pointer"
                        text="Конончик М.Н."
                        textSize="micro"
                        title="Всплывающая подсказка"
                    />

                    <AppLabel
                        :width="'w-[400px]'"
                        align="left"
                        class="cursor-pointer"
                        text="ПС 220Ж 100С 200С 220Ж микрофибра (рис. КМ)"
                        textSize="micro"
                        title="Всплывающая подсказка"
                    />


                </div>

            </div>
        </div>


    </div>


</template>

<script setup>
import {ref, watch} from 'vue'
import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
// import AppCheckbox from '/resources/js/src/components/ui/checkboxes/AppCheckbox.vue'
import AppCheckboxSimple from '/resources/js/src/components/ui/checkboxes/AppCheckboxSimple.vue'
import AppLabelMultiline from '/resources/js/src/components/ui/labels/AppLabelMultiline.vue'


const checkboxData = {
    // name: 'numbers',
    data: [
        {id: 1, name: ''},
    ]
}


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
