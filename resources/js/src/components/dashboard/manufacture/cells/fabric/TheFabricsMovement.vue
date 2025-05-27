<template>

    <div class="ml-2 mt-2">

        <!-- attract: Шапка таблицы -->
        <div class="sticky top-0 flex p-1 mb-1 bg-blue-200 border-2 rounded-lg border-blue-700 max-w-fit">

            <!-- attract: № рулона  -->
            <AppLabelMultiLine
                :text="render.rollNumber.header"
                :title="render.rollNumber.title"
                :type="render.rollNumber.type(true)"
                :width="render.rollNumber.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!-- attract: ПС  -->
            <AppLabelMultiLine
                :text="render.fabric.header"
                :title="render.fabric.title"
                :type="render.fabric.type(true)"
                :width="render.fabric.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!-- attract: Ткань, м.п. -->
            <AppLabelMultiLine
                :text="render.textileLength.header"
                :title="render.textileLength.title"
                :type="render.textileLength.type(true)"
                :width="render.textileLength.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!-- attract: ПС, м.п. -->
            <AppLabelMultiLine
                :text="render.fabricLength.header"
                :title="render.fabricLength.title"
                :type="render.fabricLength.type(true)"
                :width="render.fabricLength.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!-- attract: Дата пр-ва -->
            <AppLabelMultiLine
                :text="render.finishAt.header"
                :title="render.finishAt.title"
                :type="render.finishAt.type(true)"
                :width="render.finishAt.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!-- attract: Ответственный за производство -->
            <AppLabelMultiLine
                :text="render.finishBy.header"
                :title="render.finishBy.title"
                :type="render.finishBy.type(true)"
                :width="render.finishBy.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!-- attract: Флаг учета в 1С -->
            <AppLabelMultiLine
                :text="render.registration_1C_Flag.header"
                :title="render.registration_1C_Flag.title"
                :type="render.registration_1C_Flag.type(true)"
                :width="render.registration_1C_Flag.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!-- attract: Дата учета в 1С -->
            <AppLabelMultiLine
                :text="render.registration_1C_At.header"
                :title="render.registration_1C_At.title"
                :type="render.registration_1C_At.type(true)"
                :width="render.registration_1C_At.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!-- attract: Ответственный за учет в 1С -->
            <AppLabelMultiLine
                :text="render.registration_1C_By.header"
                :title="render.registration_1C_By.title"
                :type="render.registration_1C_By.type(true)"
                :width="render.registration_1C_By.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!-- attract: Флаг перемещения на закрой -->
            <AppLabelMultiLine
                :text="render.moveToCutFlag.header"
                :title="render.moveToCutFlag.title"
                :type="render.moveToCutFlag.type(true)"
                :width="render.moveToCutFlag.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!-- attract: Дата перемещения на закрой -->
            <AppLabelMultiLine
                :text="render.moveToCutAt.header"
                :title="render.moveToCutAt.title"
                :type="render.moveToCutAt.type(true)"
                :width="render.moveToCutAt.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!-- attract: Ответственный за перемещение на закрой -->
            <AppLabelMultiLine
                :text="render.moveToCutBy.header"
                :title="render.moveToCutBy.title"
                :type="render.moveToCutBy.type(true)"
                :width="render.moveToCutBy.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!-- attract: Примечание -->
            <AppLabelMultiLine
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
        <div class="p-1 mb-1 bg-blue-100 border-2 rounded-lg border-blue-600 max-w-fit">

            <div v-for="roll in doneRolls" :key="roll.id" class="flex">

                <!-- attract: № рулона  -->
                <AppLabel
                    :text="render.rollNumber.data(roll)"
                    :title="render.rollNumber.title"
                    :type="render.rollNumber.type(false, roll)"
                    :width="render.rollNumber.width"
                    align="center"
                    text-size="micro"
                />

                <!-- attract: ПС  -->
                <AppLabel
                    :text="render.fabric.data(roll)"
                    :title="render.fabric.title"
                    :type="render.fabric.type(false, roll)"
                    :width="render.fabric.width"
                    text-size="micro"
                />

                <!-- attract: Ткань, м.п. -->
                <AppLabel
                    :text="render.textileLength.data(roll)"
                    :title="render.textileLength.title"
                    :type="render.textileLength.type(false, roll)"
                    :width="render.textileLength.width"
                    align="center"
                    text-size="micro"
                />

                <!-- attract: ПС, м.п. -->
                <AppLabel
                    :text="render.fabricLength.data(roll)"
                    :title="render.fabricLength.title"
                    :type="render.fabricLength.type(false, roll)"
                    :width="render.fabricLength.width"
                    align="center"
                    text-size="micro"
                />

                <!-- attract: Дата пр-ва -->
                <AppLabel
                    :text="render.finishAt.data(roll)"
                    :title="render.finishAt.title"
                    :type="render.finishAt.type(false, roll)"
                    :width="render.finishAt.width"
                    align="center"
                    text-size="micro"
                />

                <!-- attract: Ответственный за производство -->
                <AppLabel
                    :text="render.finishBy.data(roll)"
                    :title="render.finishBy.title"
                    :type="render.finishBy.type(false, roll)"
                    :width="render.finishBy.width"
                    align="center"
                    text-size="micro"
                />

                <!-- attract: Флаг учета в 1С -->
                <AppLabel
                    :text="render.registration_1C_Flag.data(roll)"
                    :text-size="render.registration_1C_Flag.textSize(roll)"
                    :title="render.registration_1C_Flag.title"
                    :type="render.registration_1C_Flag.type(false, roll)"
                    :width="render.registration_1C_Flag.width"
                    align="center"
                    class="cursor-pointer"
                    @click="changeRegistrationStatus(roll)"
                />

                <!-- attract: Дата учета в 1С -->
                <AppLabel
                    :text="render.registration_1C_At.data(roll)"
                    :title="render.registration_1C_At.title"
                    :type="render.registration_1C_At.type(false, roll)"
                    :width="render.registration_1C_At.width"
                    align="center"
                    text-size="micro"
                />

                <!-- attract: Ответственный за учет в 1С -->
                <AppLabel
                    :text="render.registration_1C_By.data(roll)"
                    :title="render.registration_1C_By.title"
                    :type="render.registration_1C_By.type(false, roll)"
                    :width="render.registration_1C_By.width"
                    align="center"
                    text-size="micro"
                />

                <!-- attract: Флаг перемещения на закрой -->
                <AppLabel
                    :text="render.moveToCutFlag.data(roll)"
                    :text-size="render.moveToCutFlag.textSize(roll)"
                    :title="render.moveToCutFlag.title"
                    :type="render.moveToCutFlag.type(false, roll)"
                    :width="render.moveToCutFlag.width"
                    align="center"
                    class="cursor-pointer"
                    @click="changeMovingStatus(roll)"
                />

                <!-- attract: Дата перемещения на закрой -->
                <AppLabel
                    :text="render.moveToCutAt.data(roll)"
                    :title="render.moveToCutAt.title"
                    :type="render.moveToCutAt.type(false, roll)"
                    :width="render.moveToCutAt.width"
                    align="center"
                    text-size="micro"
                />

                <!-- attract: Ответственный за перемещение на закрой -->
                <AppLabel
                    :text="render.moveToCutBy.data(roll)"
                    :title="render.moveToCutBy.title"
                    :type="render.moveToCutBy.type(false, roll)"
                    :width="render.moveToCutBy.width"
                    align="center"
                    text-size="micro"
                />

                <!-- attract: Примечание -->
                <AppLabel
                    :text="render.description.data(roll)"
                    :title="render.description.title"
                    :type="render.description.type(false, roll)"
                    :width="render.description.width"
                    text-size="micro"
                />

            </div>

        </div>

    </div>

    <!-- attract: Модальное окно для подтверждений -->
    <AppModalAsyncMultiLine
        ref="appModalAsync"
        :text="modalText"
        :type="modalType"
        mode="confirm"
    />

    <!-- attract: Callout -->
    <AppCallout
        :show="calloutShow"
        :text="calloutText"
        :type="calloutType"
    />

</template>

<script setup>

import {reactive, ref} from 'vue'

import {useFabricsStore} from '/resources/js/src/stores/FabricsStore.js'

import {FABRIC_ROLL_STATUS} from '/resources/js/src/app/constants/fabrics.js'

import {getFormatFIO} from '/resources/js/src/app/helpers/workers/helpers_workers.js'
import {getTypeByRollStatus} from '/resources/js/src/app/helpers/manufacture/helpers_fabric.js'
import {formatDateAndTimeInShortFormat} from '/resources/js/src/app/helpers/helpers_date.js'

import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
import AppLabelMultiLine from '/resources/js/src/components/ui/labels/AppLabelMultiLine.vue'
import AppModalAsyncMultiLine from '/resources/js/src/components/ui/modals/AppModalAsyncMultiline.vue'
import AppCallout from '/resources/js/src/components/ui/callouts/AppCallout.vue'

const fabricsStore = useFabricsStore()

// attract: Получаем с API все выполненные рулоны + рулоны в 1С
const getNotAcceptedToCutRolls = async () => {
    const $rolls = await fabricsStore.getNotAcceptedToCutRolls()
    $rolls.sort((a, b) => a.status - b.status) // сортируем по возрастанию статуса
    return $rolls
}
const doneRolls = ref(await getNotAcceptedToCutRolls())

console.log('doneRolls: ', doneRolls.value)


// attract: Получаем ссылку на модальное для подтверждений окно с асинхронной функцией
const appModalAsync = ref(null)
const modalText = ref([])
const modalType = ref('danger')


// attract: Callout для вывода ошибок и предупреждений
const calloutType = ref('danger')
const calloutText = ref('')
const calloutShow = ref(false)
const calloutClose = (delay = 5000) => setTimeout(() => calloutShow.value = false, delay) // закрываем callout


// attract: Задаем глобальный объект для унификации отображения рулонов
const getTypeOfRoll = (rollStatus, flag = false) => flag ? 'primary' : getTypeByRollStatus(rollStatus)

const render = reactive({
    rollNumber: {
        header: ['№', 'рул.'],
        width: 'w-[60px]',
        show: true,
        title: 'Учетный номер рулона',
        type: (flag = false, roll) => getTypeOfRoll(roll?.status, flag),
        data: (roll) => roll.id.toString(),
    },
    fabric: {
        header: ['Полотно', 'стеганное'],
        width: 'w-[250px]',
        show: true,
        title: 'Полотно стеганное',
        type: (flag = false, roll) => getTypeOfRoll(roll?.status, flag),
        data: (roll) => roll.fabric.display_name,
    },
    textileLength: {
        header: ['Ткань', 'м.п.'],
        width: 'w-[60px]',
        show: true,
        title: 'Ткань, м.п.',
        type: (flag = false, roll) => getTypeOfRoll(roll?.status, flag),
        data: (roll) => roll.textile_length.toFixed(3),
    },
    fabricLength: {
        header: ['ПС', 'м.п.'],
        width: 'w-[60px]',
        show: true,
        title: 'ПС, м.п.',
        type: (flag = false, roll) => getTypeOfRoll(roll?.status, flag),
        data: (roll) => (roll.textile_length / roll.rate).toFixed(3),
    },
    finishAt: {
        header: ['Дата', 'пр-ва'],
        width: 'w-[100px]',
        show: true,
        title: 'Дата производства',
        type: (flag = false, roll) => getTypeOfRoll(roll?.status, flag),
        data: (roll) => formatDateAndTimeInShortFormat(roll.finish_at, false),
    },
    finishBy: {
        header: ['Ответственный за', 'производство'],
        width: 'w-[150px]',
        show: true,
        title: 'Дата производства',
        type: (flag = false, roll) => getTypeOfRoll(roll?.status, flag),
        data: (roll) => getFormatFIO(roll.finish_by),
    },
    registration_1C_Flag: {
        header: ['Учет', 'в 1С'],
        width: 'w-[60px]',
        show: true,
        title: 'Учет в 1С',
        type: (flag = false, roll) => getTypeOfRoll(roll?.status, flag),
        textSize: (roll) => roll.registration_1C_by.id !== 0 ? 'large' : 'mini',
        data: (roll) => roll.registration_1C_by.id !== 0 ? '✅' : '❌',
        // textSize: (roll) => roll.status === FABRIC_ROLL_STATUS.REGISTERED_1C.CODE ? 'large' : 'mini',
        // data: (roll) => roll.status === FABRIC_ROLL_STATUS.REGISTERED_1C.CODE ? '✅' : '❌',
    },
    registration_1C_At: {
        header: ['Дата', 'учета в 1С'],
        width: 'w-[100px]',
        show: true,
        title: 'Дата учета в 1С',
        type: (flag = false, roll) => getTypeOfRoll(roll?.status, flag),
        data: (roll) => formatDateAndTimeInShortFormat(roll.registration_1C_at, false),
    },
    registration_1C_By: {
        header: ['Ответственный за', 'учет в 1С'],
        width: 'w-[150px]',
        show: true,
        title: 'Ответственный за учет в 1С',
        type: (flag = false, roll) => getTypeOfRoll(roll?.status, flag),
        data: (roll) => roll.registration_1C_by.id !== 0 ? roll.registration_1C_by.name : '',
        // data: (roll) => roll.registration_1C_by.id !== 0 ?  getFormatFIO(roll.registration_1C_by) : '',
    },
    moveToCutFlag: {
        header: ['--->', 'закрой'],
        width: 'w-[60px]',
        show: true,
        title: 'Перемещение на закрой',
        type: (flag = false, roll) => getTypeOfRoll(roll?.status, flag),
        textSize: (roll) => roll.status === FABRIC_ROLL_STATUS.MOVED.CODE ? 'large' : 'mini',
        data: (roll) => roll.status === FABRIC_ROLL_STATUS.MOVED.CODE ? '✅' : '❌',
    },
    moveToCutAt: {
        header: ['Дата', '--->'],
        width: 'w-[100px]',
        show: true,
        title: 'Дата перемещения на закрой',
        type: (flag = false, roll) => getTypeOfRoll(roll?.status, flag),
        data: (roll) => formatDateAndTimeInShortFormat(roll.move_to_cut_at, false),
    },
    moveToCutBy: {
        header: ['Ответственный за', '---> на закрой'],
        width: 'w-[150px]',
        show: true,
        title: 'Ответственный за перемещение на закрой',
        type: (flag = false, roll) => getTypeOfRoll(roll?.status, flag),
        data: (roll) => roll.move_to_cut_by.id !== 0 ? roll.move_to_cut_by.name : '',
        // data: (roll) => roll.move_to_cut_by.id !== 0 ? getFormatFIO(roll.move_to_cut_by) : '',
    },
    description: {
        header: ['Примечание', ''],
        width: 'w-[400px]',
        show: true,
        title: 'Примечание',
        type: (flag = false, roll) => getTypeOfRoll(roll?.status, flag),
        data: (roll) => roll.descr,
    },

})


// attract: Ставим на учет в 1С
const changeRegistrationStatus = async (roll) => {
    // console.log(roll)
    if (roll.status === FABRIC_ROLL_STATUS.MOVED.CODE) {
        calloutType.value = 'danger'
        calloutText.value = 'Рулон уже перемещен на закрой.'
        calloutShow.value = true
        calloutClose()
        return
    }

    if (roll.status === FABRIC_ROLL_STATUS.DONE.CODE) {

        modalText.value = ['Будет установлен статус учета рулона в 1С.', 'Продолжить?']
        modalType.value = FABRIC_ROLL_STATUS.REGISTERED_1C.TYPE

        const answer = await appModalAsync.value.show() // показываем модалку и ждем ответ
        if (answer) {
            roll.status = FABRIC_ROLL_STATUS.REGISTERED_1C.CODE
            const res = await fabricsStore.setRollRegisteredStatus(roll.id, roll.status)
            // console.log(res)
            doneRolls.value = await getNotAcceptedToCutRolls()
        }

    } else if (roll.status === FABRIC_ROLL_STATUS.REGISTERED_1C.CODE) {

        modalText.value = ['Будет снят статус учета рулона в 1С.', 'Продолжить?']
        modalType.value = FABRIC_ROLL_STATUS.DONE.TYPE

        const answer = await appModalAsync.value.show() // показываем модалку и ждем ответ
        if (answer) {
            roll.status = FABRIC_ROLL_STATUS.DONE.CODE
            const res = await fabricsStore.setRollRegisteredStatus(roll.id, roll.status)
            // console.log(res)
            doneRolls.value = await getNotAcceptedToCutRolls()
        }

    }

}


// attract: Перемещаем на закрой
const changeMovingStatus = async (roll) => {
    // console.log(roll)

    // Проверяем что рулон учтен в 1С
    if (roll.registration_1C_by.id === 0) {
        calloutType.value = 'danger'
        calloutText.value = 'Рулон не может быть перемещен на закрой без учета в 1С.'
        calloutShow.value = true
        calloutClose()
        return
    }

    if (roll.status === FABRIC_ROLL_STATUS.REGISTERED_1C.CODE) {

        modalText.value = ['Рулон будет перемещен на закрой.', 'Продолжить?']
        modalType.value = FABRIC_ROLL_STATUS.MOVED.TYPE

        const answer = await appModalAsync.value.show() // показываем модалку и ждем ответ
        if (answer) {
            roll.status = FABRIC_ROLL_STATUS.MOVED.CODE
            const res = await fabricsStore.setRollMovedStatus(roll.id, roll.status)
            console.log(res)
            doneRolls.value = await getNotAcceptedToCutRolls()
        }

    } else if (roll.status === FABRIC_ROLL_STATUS.MOVED.CODE) {

        modalText.value = ['Рулон будет перемещен на стежку.', 'Продолжить?']
        modalType.value = FABRIC_ROLL_STATUS.REGISTERED_1C.TYPE

        const answer = await appModalAsync.value.show() // показываем модалку и ждем ответ
        if (answer) {
            roll.status = FABRIC_ROLL_STATUS.REGISTERED_1C.CODE
            const res = await fabricsStore.setRollMovedStatus(roll.id, roll.status)
            console.log(res)
            doneRolls.value = await getNotAcceptedToCutRolls()
        }

    }

}

</script>

<style scoped>

.header-item {

}

</style>
