<template>

    <div class="ml-2 mt-2">

        <!-- attract: Шапка таблицы -->
        <div class="sticky top-0 flex pt-1 pb-1 bg-blue-200 border-2 rounded-lg border-blue-700 p-1 mb-1 max-w-fit">

            <!-- attract: № рулона  -->
            <AppLabelMultiline
                :text="render.rollNumber.header"
                :title="render.rollNumber.title"
                :type="render.rollNumber.type(true)"
                :width="render.rollNumber.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!-- attract: ПС  -->
            <AppLabelMultiline
                :text="render.fabric.header"
                :title="render.fabric.title"
                :type="render.fabric.type(true)"
                :width="render.fabric.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!-- attract: Ткань, м.п. -->
            <AppLabelMultiline
                :text="render.textileLength.header"
                :title="render.textileLength.title"
                :type="render.textileLength.type(true)"
                :width="render.textileLength.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!-- attract: ПС, м.п. -->
            <AppLabelMultiline
                :text="render.fabricLength.header"
                :title="render.fabricLength.title"
                :type="render.fabricLength.type(true)"
                :width="render.fabricLength.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!-- attract: Дата пр-ва -->
            <AppLabelMultiline
                :text="render.finishAt.header"
                :title="render.finishAt.title"
                :type="render.finishAt.type(true)"
                :width="render.finishAt.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!-- attract: Ответственный за производство -->
            <AppLabelMultiline
                :text="render.finishBy.header"
                :title="render.finishBy.title"
                :type="render.finishBy.type(true)"
                :width="render.finishBy.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!-- attract: Флаг учета в 1С -->
            <AppLabelMultiline
                :text="render.registration_1C_Flag.header"
                :title="render.registration_1C_Flag.title"
                :type="render.registration_1C_Flag.type(true)"
                :width="render.registration_1C_Flag.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!-- attract: Дата учета в 1С -->
            <AppLabelMultiline
                :text="render.registration_1C_At.header"
                :title="render.registration_1C_At.title"
                :type="render.registration_1C_At.type(true)"
                :width="render.registration_1C_At.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!-- attract: Ответственный за учет в 1С -->
            <AppLabelMultiline
                :text="render.registration_1C_By.header"
                :title="render.registration_1C_By.title"
                :type="render.registration_1C_By.type(true)"
                :width="render.registration_1C_By.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!-- attract: Флаг перемещения на закрой -->
            <AppLabelMultiline
                :text="render.moveToCutFlag.header"
                :title="render.moveToCutFlag.title"
                :type="render.moveToCutFlag.type(true)"
                :width="render.moveToCutFlag.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!-- attract: Дата перемещения на закрой -->
            <AppLabelMultiline
                :text="render.moveToCutAt.header"
                :title="render.moveToCutAt.title"
                :type="render.moveToCutAt.type(true)"
                :width="render.moveToCutAt.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!-- attract: Ответственный за перемещение на закрой -->
            <AppLabelMultiline
                :text="render.moveToCutBy.header"
                :title="render.moveToCutBy.title"
                :type="render.moveToCutBy.type(true)"
                :width="render.moveToCutBy.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!-- attract: Примечание -->
            <AppLabelMultiline
                :text="render.description.header"
                :title="render.description.title"
                :type="render.description.type(true)"
                :width="render.description.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

        </div>


        <!-- attract: Сами данные -->
        <div class="ml-1">

            <div v-for="roll in doneRolls">

                <div class="flex">

                    <!-- attract: № рулона  -->
                    <AppLabel
                        :text="render.rollNumber.data(roll)"
                        :title="render.rollNumber.title"
                        :type="render.rollNumber.type()"
                        :width="render.rollNumber.width"
                        align="center"
                        text-size="micro"
                    />

                    <!-- attract: ПС  -->
                    <AppLabel
                        :text="render.fabric.data(roll)"
                        :title="render.fabric.title"
                        :type="render.fabric.type()"
                        :width="render.fabric.width"
                        text-size="micro"
                    />

                    <!-- attract: Ткань, м.п. -->
                    <AppLabel
                        :text="render.textileLength.data(roll)"
                        :title="render.textileLength.title"
                        :type="render.textileLength.type()"
                        :width="render.textileLength.width"
                        align="center"
                        text-size="micro"
                    />

                    <!-- attract: ПС, м.п. -->
                    <AppLabel
                        :text="render.fabricLength.data(roll)"
                        :title="render.fabricLength.title"
                        :type="render.fabricLength.type()"
                        :width="render.fabricLength.width"
                        align="center"
                        text-size="micro"
                    />

                    <!-- attract: Дата пр-ва -->
                    <AppLabel
                        :text="render.finishAt.data(roll)"
                        :title="render.finishAt.title"
                        :type="render.finishAt.type()"
                        :width="render.finishAt.width"
                        align="center"
                        text-size="micro"
                    />


                    <!-- attract: Ответственный за производство -->
                    <AppLabel
                        :text="render.finishBy.data(roll)"
                        :title="render.finishBy.title"
                        :type="render.finishBy.type()"
                        :width="render.finishBy.width"
                        align="center"
                        text-size="micro"
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

                    <!-- attract: Флаг учета в 1С -->
                    <AppCheckboxSimple
                        :width="'w-[64px]'"
                    />

                    <!-- attract: Дата учета в 1С -->
                    <AppLabel
                        :text="render.registration_1C_At.data(roll)"
                        :title="render.registration_1C_At.title"
                        :type="render.registration_1C_At.type()"
                        :width="render.registration_1C_At.width"
                        align="center"
                        text-size="micro"
                    />

                    <!-- attract: Ответственный за учет в 1С -->
                    <AppLabel
                        :text="render.registration_1C_By.data(roll)"
                        :title="render.registration_1C_By.title"
                        :type="render.registration_1C_By.type()"
                        :width="render.registration_1C_By.width"
                        align="center"
                        text-size="micro"
                    />

                    <!-- attract: Флаг перемещения на закрой -->
                    <AppCheckboxSimple
                        :width="'w-[64px]'"
                    />

                    <!-- attract: Дата перемещения на закрой -->
                    <AppLabel
                        :text="render.moveToCutAt.data(roll)"
                        :title="render.moveToCutAt.title"
                        :type="render.moveToCutAt.type()"
                        :width="render.moveToCutAt.width"
                        align="center"
                        text-size="micro"
                    />

                    <!-- attract: Ответственный за перемещение на закрой -->
                    <AppLabel
                        :text="render.moveToCutBy.data(roll)"
                        :title="render.moveToCutBy.title"
                        :type="render.moveToCutBy.type()"
                        :width="render.moveToCutBy.width"
                        align="center"
                        text-size="micro"
                    />

                    <!-- attract: Примечание -->
                    <AppLabel
                        :text="render.description.data(roll)"
                        :title="render.description.title"
                        :type="render.description.type()"
                        :width="render.description.width"
                        align="center"
                        text-size="micro"
                    />

                </div>

            </div>

        </div>

    </div>


</template>

<script setup>

import {reactive, ref, watch} from 'vue'

import {useFabricsStore} from '/resources/js/src/stores/FabricsStore.js'

import {FABRIC_ROLL_STATUS} from '/resources/js/src/app/constants/fabrics.js'

import {getFormatFIO} from '/resources/js/src/app/helpers/workers/helpers_workers.js'
import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
import AppCheckboxSimple from '/resources/js/src/components/ui/checkboxes/AppCheckboxSimple.vue'
import AppLabelMultiline from '/resources/js/src/components/ui/labels/AppLabelMultiline.vue'
// import AppCheckbox from '/resources/js/src/components/ui/checkboxes/AppCheckbox.vue'


const fabricsStore = useFabricsStore()

// attract: Получаем с API все выполненные рулоны
const getDoneRolls = async () => await fabricsStore.getDoneRolls()
const doneRolls = ref(await getDoneRolls())

console.log('doneRolls: ', doneRolls.value)


const checkboxData = {
    // name: 'numbers',
    data: [
        {id: 1, name: ''},
    ]
}


// attract: Задаем глобальный объект для унификации отображения рулонов
const render = reactive({
    rollNumber: {
        header: ['№', 'рул.'],
        width: 'w-[60px]',
        show: true,
        title: 'Учетный номер рулона',
        type: (flag = false) => flag ? 'primary' : 'dark',
        data: (roll) => roll.id.toString(),
    },
    fabric: {
        header: ['Полотно', 'стеганное'],
        width: 'w-[250px]',
        show: true,
        title: 'Полотно стеганное',
        type: (flag = false) => flag ? 'primary' : 'dark',
        data: (roll) => roll.fabric.display_name,
    },
    textileLength: {
        header: ['Ткань', 'м.п.'],
        width: 'w-[70px]',
        show: true,
        title: 'Ткань, м.п.',
        type: (flag = false) => flag ? 'primary' : 'dark',
        data: (roll) => roll.textile_length.toFixed(3),
    },
    fabricLength: {
        header: ['ПС', 'м.п.'],
        width: 'w-[70px]',
        show: true,
        title: 'ПС, м.п.',
        type: (flag = false) => flag ? 'primary' : 'dark',
        data: (roll) => (roll.textile_length / roll.rate).toFixed(3),
    },
    finishAt: {
        header: ['Дата', 'пр-ва'],
        width: 'w-[110px]',
        show: true,
        title: 'Дата производства',
        type: (flag = false) => flag ? 'primary' : 'dark',
        data: (roll) => roll.finish_at,
    },
    finishBy: {
        header: ['Ответственный за', 'производство'],
        width: 'w-[150px]',
        show: true,
        title: 'Дата производства',
        type: (flag = false) => flag ? 'primary' : 'dark',
        data: (roll) => getFormatFIO(roll.finish_by),
    },
    registration_1C_Flag: {
        header: ['Учет', 'в 1С'],
        width: 'w-[60px]',
        show: true,
        title: 'Учет в 1С',
        type: (flag = false) => flag ? 'primary' : 'dark',
        data: (roll) => roll.registration_1C_at !== null,
    },
    registration_1C_At: {
        header: ['Дата', 'учета в 1С'],
        width: 'w-[100px]',
        show: true,
        title: 'Дата учета в 1С',
        type: (flag = false) => flag ? 'primary' : 'dark',
        data: (roll) => roll.registration_1C_at,
    },
    registration_1C_By: {
        header: ['Ответственный за', 'учет в 1С'],
        width: 'w-[150px]',
        show: true,
        title: 'Ответственный за учет в 1С',
        type: (flag = false) => flag ? 'primary' : 'dark',
        data: (roll) => 'Не реализовано',
        // data: (roll) => roll.registration_1C_by.id !== 0 ?  getFormatFIO(roll.registration_1C_by) : '',
    },
    moveToCutFlag: {
        header: ['--->', 'закрой'],
        width: 'w-[60px]',
        show: true,
        title: 'Перемещение на закрой',
        type: (flag = false) => flag ? 'primary' : 'dark',
        data: (roll) => roll.status === FABRIC_ROLL_STATUS.MOVED.CODE,
    },
    moveToCutAt: {
        header: ['Дата', '--->'],
        width: 'w-[100px]',
        show: true,
        title: 'Дата перемещения на закрой',
        type: (flag = false) => flag ? 'primary' : 'dark',
        data: (roll) => roll.move_to_cut_at,
    },
    moveToCutBy: {
        header: ['Ответственный за', '--->'],
        width: 'w-[150px]',
        show: true,
        title: 'Ответственный за перемещение на закрой',
        type: (flag = false) => flag ? 'primary' : 'dark',
        data: (roll) => roll.move_to_cut_by.id !== 0 ?  getFormatFIO(roll.move_to_cut_by) : '',
    },
    description: {
        header: ['Примечание', ''],
        width: 'w-[400px]',
        show: true,
        title: 'Примечание',
        type: (flag = false) => flag ? 'primary' : 'dark',
        data: (roll) => roll.descr,
    },

    //

})


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

.header-item {
    @apply border-2 rounded-lg border-blue-700
}


</style>
