<template>
    <div class="ml-2 mt-2">
        <!-- attract Выводим табы, если СМ активна или статус СЗ - "Выполнено" или "Выполняется" -->
        <div class="flex flex-row justify-start items-center mb-2">
            <div
                v-for="tab in tabs"
                :key="tab.id"
                class="m-0.5"
            >
                <div
                    v-if="tab.enabled"
                    :class="tab.shown ? 'p-1 bg-blue-200 border-2 rounded-lg border-blue-700' : ''"
                >
                    <AppLabelMultiLine
                        :bold="true"
                        :text="tab.name"
                        :type="tab.type"
                        align="center"
                        class="cursor-pointer"
                        width="w-[200px]"
                        @click="changeTab(tab)"
                    />
                </div>
            </div>
        </div>

        <!-- attract: Шапка таблицы -->
        <div class="sticky top-0 flex p-1 mb-1 bg-blue-200 border-2 rounded-lg border-blue-700 max-w-fit">
            <div>
                <!-- attract: № рулона  -->
                <AppLabelMultiLine
                    v-if="render.rollNumber.show"
                    :text="render.rollNumber.header"
                    :title="render.rollNumber.title"
                    :type="render.rollNumber.type(true)"
                    :width="render.rollNumber.width"
                    align="center"
                    class="header-item"
                    text-size="mini"
                />

                <!-- attract: Фильтр: № рулона -->
                <AppInputText
                    v-if="render.rollNumber.show"
                    id="roll-number-search"
                    v-model.trim="rollNumberFilter"
                    :width="render.rollNumber.width"
                    placeholder="№ рул."
                    text-size="mini"
                    type="primary"
                    @input="handleRollNumberInput"
                />
            </div>

            <div>
                <!-- attract: ПС  -->
                <AppLabelMultiLine
                    v-if="render.fabric.show"
                    :text="render.fabric.header"
                    :title="render.fabric.title"
                    :type="render.fabric.type(true)"
                    :width="render.fabric.width"
                    align="center"
                    class="header-item"
                    text-size="mini"
                />

                <!-- attract: Фильтр: ПС -->
                <AppInputText
                    v-if="render.fabric.show"
                    id="fabric-search"
                    v-model.trim="fabricFilter"
                    :width="render.fabric.width"
                    placeholder="ПС"
                    text-size="mini"
                    type="primary"
                />
            </div>

            <div>
                <div class="flex">
                    <!-- attract: Ткань, м.п. -->
                    <AppLabelMultiLine
                        v-if="render.textileLength.show"
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
                        v-if="render.fabricLength.show"
                        :text="render.fabricLength.header"
                        :title="render.fabricLength.title"
                        :type="render.fabricLength.type(true)"
                        :width="render.fabricLength.width"
                        align="center"
                        class="header-item"
                        text-size="mini"
                    />
                </div>

                <div v-if="tabs.common.shown">
                    <!-- attract: Фильтр: Статус рулонов. Показываем только на общей вкладке -->
                    <AppSelectSimple
                        :select-data="rollStatuses"
                        :type="statusFilterType"
                        text-size="mini"
                        width="w-[124px]"
                        @change="filterByStatus"
                    />
                </div>
            </div>

            <div>
                <!-- attract: Дата пр-ва -->
                <AppLabelMultiLine
                    v-if="render.finishAt.show"
                    :text="render.finishAt.header"
                    :title="render.finishAt.title"
                    :type="render.finishAt.type(true)"
                    :width="render.finishAt.width"
                    align="center"
                    class="header-item"
                    text-size="mini"
                />

                <!-- attract: Фильтр: Дата пр-ва -->
                <AppInputText
                    v-if="render.finishAt.show"
                    id="finish-at-search"
                    v-model.trim="finishAtFilter"
                    :width="render.finishAt.width"
                    placeholder="дд.мм.гггг"
                    text-size="mini"
                    type="primary"
                />
            </div>

            <!-- attract: Ответственный за производство -->
            <AppLabelMultiLine
                v-if="render.finishBy.show"
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
                v-if="render.registration_1C_Flag.show"
                :text="render.registration_1C_Flag.header"
                :title="render.registration_1C_Flag.title"
                :type="render.registration_1C_Flag.type(true)"
                :width="render.registration_1C_Flag.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <div>
                <!-- attract: Дата учета в 1С -->
                <AppLabelMultiLine
                    v-if="render.registration_1C_At.show"
                    :text="render.registration_1C_At.header"
                    :title="render.registration_1C_At.title"
                    :type="render.registration_1C_At.type(true)"
                    :width="render.registration_1C_At.width"
                    align="center"
                    class="header-item"
                    text-size="mini"
                />

                <!-- attract: Фильтр: Дата учета в 1С -->
                <!--                <AppInputText-->
                <!--                    v-if="render.registration_1C_At.show"-->
                <!--                    id="register-at-search"-->
                <!--                    v-model.trim="register1CAtFilter"-->
                <!--                    :width="render.registration_1C_At.width"-->
                <!--                    placeholder="дд.мм.гггг"-->
                <!--                    text-size="mini"-->
                <!--                    type="primary"-->
                <!--                />-->
            </div>

            <!-- attract: Ответственный за учет в 1С -->
            <AppLabelMultiLine
                v-if="render.registration_1C_By.show"
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
                v-if="render.moveToCutFlag.show"
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
                v-if="render.moveToCutAt.show"
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
                v-if="render.moveToCutBy.show"
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
                v-if="render.description.show"
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
        <div
            v-if="doneRolls.length"
            :key="updateTemplate"
            class="p-1 mb-1 bg-blue-100 border-2 rounded-lg border-blue-600 max-w-fit"
        >
            <div
                v-for="fabricRolls in doneRolls"
                :key="fabricRolls.fabric.id"
                class="mt-2"
            >
                <div class="flex">
                    <!-- attract: +/- -->
                    <AppLabel
                        :text="fabricRolls.fabric.collapsed ? '+' : '-'"
                        :width="render.rollNumber.width"
                        align="center"
                        class="cursor-pointer"
                        text-size="large"
                        type="stone"
                        @click="toggleCollapse(fabricRolls)"
                    />

                    <!-- attract: ПС - Название группы -->
                    <AppLabel
                        :text="fabricRolls.fabric.displayName"
                        :title="fabricRolls.fabric.displayName"
                        :width="render.fabric.width"
                        align="left"
                        class="truncate cursor-pointer"
                        text-size="mini"
                        type="stone"
                        @click="toggleCollapse(fabricRolls)"
                    />

                    <!-- attract: Ткань группы, м.п. -->
                    <AppLabel
                        :text="fabricRolls.fabric.textileLength.toFixed(3)"
                        :title="render.textileLength.title"
                        :width="render.textileLength.width"
                        align="center"
                        class="cursor-pointer"
                        text-size="mini"
                        type="stone"
                        @click="toggleCollapse(fabricRolls)"
                    />

                    <!-- attract: ПС группы, м.п. -->
                    <AppLabel
                        :text="fabricRolls.fabric.fabricLength.toFixed(3)"
                        :title="render.fabricLength.title"
                        :width="render.fabricLength.width"
                        align="center"
                        class="cursor-pointer"
                        text-size="mini"
                        type="stone"
                        @click="toggleCollapse(fabricRolls)"
                    />
                </div>

                <!-- attract: Показываем группу -->
                <div>
                    <div v-if="!fabricRolls.fabric.collapsed">
                        <div
                            v-for="roll in fabricRolls.rolls"
                            :key="roll.id"
                            class="flex"
                        >
                            <!-- attract: № рулона  -->
                            <AppLabel
                                v-if="render.rollNumber.show"
                                :text="render.rollNumber.data(roll)"
                                :title="render.rollNumber.title"
                                :type="render.rollNumber.type(false, roll)"
                                :width="render.rollNumber.width"
                                align="center"
                                text-size="micro"
                            />

                            <!-- attract: ПС  -->
                            <AppLabel
                                v-if="render.fabric.show"
                                :text="render.fabric.data(roll)"
                                :title="render.fabric.title"
                                :type="render.fabric.type(false, roll)"
                                :width="render.fabric.width"
                                text-size="micro"
                            />

                            <!-- attract: Ткань, м.п. -->
                            <AppLabel
                                v-if="render.textileLength.show"
                                :text="render.textileLength.data(roll)"
                                :title="render.textileLength.title"
                                :type="render.textileLength.type(false, roll)"
                                :width="render.textileLength.width"
                                align="center"
                                text-size="micro"
                            />

                            <!-- attract: ПС, м.п. -->
                            <AppLabel
                                v-if="render.fabricLength.show"
                                :text="render.fabricLength.data(roll)"
                                :title="render.fabricLength.title"
                                :type="render.fabricLength.type(false, roll)"
                                :width="render.fabricLength.width"
                                align="center"
                                text-size="micro"
                            />

                            <!-- attract: Дата пр-ва -->
                            <AppLabel
                                v-if="render.finishAt.show"
                                :text="render.finishAt.data(roll)"
                                :title="render.finishAt.title"
                                :type="render.finishAt.type(false, roll)"
                                :width="render.finishAt.width"
                                align="center"
                                text-size="micro"
                            />

                            <!-- attract: Ответственный за производство -->
                            <AppLabel
                                v-if="render.finishBy.show"
                                :text="render.finishBy.data(roll)"
                                :title="render.finishBy.title"
                                :type="render.finishBy.type(false, roll)"
                                :width="render.finishBy.width"
                                align="center"
                                text-size="micro"
                            />

                            <!-- attract: Флаг учета в 1С -->
                            <AppLabel
                                v-if="render.registration_1C_Flag.show"
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
                                v-if="render.registration_1C_At.show"
                                :text="render.registration_1C_At.data(roll)"
                                :title="render.registration_1C_At.title"
                                :type="render.registration_1C_At.type(false, roll)"
                                :width="render.registration_1C_At.width"
                                align="center"
                                text-size="micro"
                            />

                            <!-- attract: Ответственный за учет в 1С -->
                            <AppLabel
                                v-if="render.registration_1C_By.show"
                                :text="render.registration_1C_By.data(roll)"
                                :title="render.registration_1C_By.title"
                                :type="render.registration_1C_By.type(false, roll)"
                                :width="render.registration_1C_By.width"
                                align="center"
                                text-size="micro"
                            />

                            <!-- attract: Флаг перемещения на закрой -->
                            <AppLabel
                                v-if="render.moveToCutFlag.show"
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
                                v-if="render.moveToCutAt.show"
                                :text="render.moveToCutAt.data(roll)"
                                :title="render.moveToCutAt.title"
                                :type="render.moveToCutAt.type(false, roll)"
                                :width="render.moveToCutAt.width"
                                align="center"
                                text-size="micro"
                            />

                            <!-- attract: Ответственный за перемещение на закрой -->
                            <AppLabel
                                v-if="render.moveToCutBy.show"
                                :text="render.moveToCutBy.data(roll)"
                                :title="render.moveToCutBy.title"
                                :type="render.moveToCutBy.type(false, roll)"
                                :width="render.moveToCutBy.width"
                                align="center"
                                text-size="micro"
                            />

                            <!-- attract: Примечание -->
                            <AppLabel
                                v-if="render.description.show"
                                :text="render.description.data(roll)"
                                :title="render.description.title"
                                :type="render.description.type(false, roll)"
                                :width="render.description.width"
                                text-size="micro"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else>
            <div class="mt-5">
                <!-- attract: Примечание -->
                <AppLabel
                    align="center"
                    height="h-[100px]"
                    text="Данные не найдены..."
                    text-size="huge"
                    type="warning"
                    width="w-[300px]"
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
import {reactive, ref, watch} from 'vue'

import {useFabricsStore} from '@/stores/FabricsStore.js'

import {FABRIC_ROLL_STATUS} from '@/app/constants/fabrics.js'

import {getFormatFIO} from '@/app/helpers/workers/helpers_workers.js'
import {getTypeByRollStatus} from '@/app/helpers/manufacture/helpers_fabric.js'
import {formatDateAndTimeInShortFormat, getDateFromDateTimeString} from '@/app/helpers/helpers_date.js'

import AppLabel from '@/components/ui/labels/AppLabel.vue'
import AppInputText from '@/components/ui/inputs/AppInputText.vue'
import AppSelectSimple from '@/components/ui/selects/AppSelectSimple.vue'
import AppLabelMultiLine from '@/components/ui/labels/AppLabelMultiLine.vue'
import AppModalAsyncMultiLine from '@/components/ui/modals/AppModalAsyncMultiline.vue'
import AppCallout from '@/components/ui/callouts/AppCallout.vue'

const fabricsStore = useFabricsStore()

// attract: Задаем отображение вкладок (Общие данные, Американец, Немец, Китаец, Кореец)
const COMMON_CODE = -1
const tabs = reactive({
    common: {
        id: 1,
        shown: false,
        enabled: true,
        name: ['Учет', 'общий'],
        type: 'warning',
        code: COMMON_CODE,
    },
    done: {
        id: 2,
        shown: false,
        enabled: true,
        name: ['Учет', 'выполненных рулонов'],
        type: FABRIC_ROLL_STATUS.DONE.TYPE,
        code: FABRIC_ROLL_STATUS.DONE.CODE,
    },
    registered_1C: {
        id: 3,
        shown: false,
        enabled: true,
        name: ['Учет поставленных', 'в 1С рулонов'],
        type: FABRIC_ROLL_STATUS.REGISTERED_1C.TYPE,
        code: FABRIC_ROLL_STATUS.REGISTERED_1C.CODE,
    },
    moved: {
        id: 4,
        shown: false,
        enabled: false,
        name: ['Учет перемещенных', 'на раскрой рулонов'],
        type: FABRIC_ROLL_STATUS.MOVED.TYPE,
        code: FABRIC_ROLL_STATUS.MOVED.CODE,
    },
})

// attract: переключаем выбранную вкладку
const changeTab = (selectedTab) => {
    for (const tab in tabs) {
        if (tabs.hasOwnProperty(tab)) {
            tabs[tab].shown = tabs[tab].id === selectedTab.id
        }
    }
    // console.log(selectedTab)
}

tabs.common.shown = true // делаем вкладку "общие данные" активной, чтобы запустить реактивность

// attract: Префикс для сохранения состояния в localStorage
const FABRIC_COLLAPSE_PREFIX = 'fabric-collapse-'
const updateTemplate = ref(0)

// attract: Получаем название поля для хранилища для сворачивания/разворачивания по ПС
const getFabricStorageName = (fabricId) => `${FABRIC_COLLAPSE_PREFIX}${fabricId}`

// attract: Получаем отображаемые рулоны в зависимости от статуса группируя по ПС
const getRollsByStatus = (inRolls = [], status = COMMON_CODE) => {
    // копируем массив рулонов, чтобы не менять оригинал
    let $rolls = JSON.parse(JSON.stringify(inRolls))

    // если передан статус, то фильтруем массив рулонов по статусу
    if (status !== COMMON_CODE) $rolls = $rolls.filter((roll) => roll.status === status)

    // создаем массив уникальных id ПС
    const uniqueFabricIds = new Set()
    $rolls.forEach((roll) => {
        uniqueFabricIds.add(roll.fabric.fabric_id)
    })

    // const uniqueFabricIdsArray = Array.from(uniqueFabricIds)

    //attract: создаем массив объектов с рулонами по ПС с группировкой по ПС и сортировкой по id
    const resultArray = []

    for (const fabricId of uniqueFabricIds) {
        const fabricRolls = $rolls.filter((roll) => roll.fabric.fabric_id === fabricId).sort((a, b) => a.id - b.id)
        const fabricTextileLength = fabricRolls.reduce((amount, roll) => amount + roll.textile_length, 0)
        const fabricLength = fabricRolls.reduce((amount, roll) => amount + roll.textile_length / roll.rate, 0)

        resultArray.push({
            fabric: {
                id: fabricId,
                displayName: fabricRolls[0].fabric.display_name,
                textileLength: fabricTextileLength,
                fabricLength: fabricLength,
                collapsed: localStorage.getItem(getFabricStorageName(fabricId)) === 'true', // для сворачивания/разворачивания, запоминаем состояние
            },

            rolls: fabricRolls,
        })
    }

    return resultArray
}

// attract: Получаем с API все выполненные рулоны + рулоны в 1С + рулоны на закрой
const getNotAcceptedToCutRolls = async () => {
    // return await fabricsStore.getNotAcceptedToCutRolls()
    const rolls = await fabricsStore.getNotAcceptedToCutRolls()
    return rolls.filter((roll) => roll.status !== FABRIC_ROLL_STATUS.MOVED.CODE) //  рулоны в 1С + рулоны на закрой
}

const allRolls = ref(await getNotAcceptedToCutRolls()) // получаем все рулоны c API
const doneRolls = ref(getRollsByStatus(allRolls.value)) // преобразуем в структуру для отображения
// console.log('doneRolls: ', doneRolls.value)

// attract: Получаем ссылку на модальное для подтверждений окно с асинхронной функцией
const appModalAsync = ref(null)
const modalText = ref([])
const modalType = ref('danger')

// attract: Callout для вывода ошибок и предупреждений
const calloutType = ref('danger')
const calloutText = ref('')
const calloutShow = ref(false)
const calloutClose = (delay = 5000) => setTimeout(() => (calloutShow.value = false), delay) // закрываем callout

// attract: Задаем глобальный объект для унификации отображения рулонов
const getTypeOfRoll = (rollStatus, flag = false) => (flag ? 'primary' : getTypeByRollStatus(rollStatus))

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
        textSize: (roll) => (roll.registration_1C_by.id !== 0 ? 'large' : 'mini'),
        data: (roll) => (roll.registration_1C_by.id !== 0 ? '✅' : '❌'),
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
        data: (roll) => (roll.registration_1C_by.id !== 0 ? roll.registration_1C_by.name : ''),
        // data: (roll) => roll.registration_1C_by.id !== 0 ?  getFormatFIO(roll.registration_1C_by) : '',
    },
    moveToCutFlag: {
        header: ['--->', 'закрой'],
        width: 'w-[60px]',
        show: false,
        title: 'Перемещение на закрой',
        type: (flag = false, roll) => getTypeOfRoll(roll?.status, flag),
        textSize: (roll) => (roll.status === FABRIC_ROLL_STATUS.MOVED.CODE ? 'large' : 'mini'),
        data: (roll) => (roll.status === FABRIC_ROLL_STATUS.MOVED.CODE ? '✅' : '❌'),
    },
    moveToCutAt: {
        header: ['Дата', '--->'],
        width: 'w-[100px]',
        show: false,
        title: 'Дата перемещения на закрой',
        type: (flag = false, roll) => getTypeOfRoll(roll?.status, flag),
        data: (roll) => formatDateAndTimeInShortFormat(roll.move_to_cut_at, false),
    },
    moveToCutBy: {
        header: ['Ответственный за', '---> на закрой'],
        width: 'w-[150px]',
        show: false,
        title: 'Ответственный за перемещение на закрой',
        type: (flag = false, roll) => getTypeOfRoll(roll?.status, flag),
        data: (roll) => (roll.move_to_cut_by.id !== 0 ? roll.move_to_cut_by.name : ''),
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

// attract: Создаем статусы рулонов для фильтра
const rollStatuses = {
    name: 'statuses',
    data: [
        {id: 0, name: 'Все статусы', selected: true, disabled: false},
        {id: FABRIC_ROLL_STATUS.DONE.CODE, name: FABRIC_ROLL_STATUS.DONE.TITLE, selected: false, disabled: false},
        {
            id: FABRIC_ROLL_STATUS.REGISTERED_1C.CODE,
            name: FABRIC_ROLL_STATUS.REGISTERED_1C.TITLE,
            selected: false,
            disabled: false,
        },
    ],
}

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
            allRolls.value = await getNotAcceptedToCutRolls()
            // console.log(res)
        }
    } else if (roll.status === FABRIC_ROLL_STATUS.REGISTERED_1C.CODE) {
        modalText.value = ['Будет снят статус учета рулона в 1С.', 'Продолжить?']
        modalType.value = FABRIC_ROLL_STATUS.DONE.TYPE

        const answer = await appModalAsync.value.show() // показываем модалку и ждем ответ
        if (answer) {
            roll.status = FABRIC_ROLL_STATUS.DONE.CODE
            const res = await fabricsStore.setRollRegisteredStatus(roll.id, roll.status)
            allRolls.value = await getNotAcceptedToCutRolls()
            // console.log(res)
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
            allRolls.value = await getNotAcceptedToCutRolls()
            // console.log(res)
        }
    } else if (roll.status === FABRIC_ROLL_STATUS.MOVED.CODE) {
        modalText.value = ['Рулон будет перемещен на стежку.', 'Продолжить?']
        modalType.value = FABRIC_ROLL_STATUS.REGISTERED_1C.TYPE

        const answer = await appModalAsync.value.show() // показываем модалку и ждем ответ
        if (answer) {
            roll.status = FABRIC_ROLL_STATUS.REGISTERED_1C.CODE
            const res = await fabricsStore.setRollMovedStatus(roll.id, roll.status)
            allRolls.value = await getNotAcceptedToCutRolls()
            // console.log(res)
        }
    }
}

// attract: Фильтры
const fabricFilter = ref('')
const rollNumberFilter = ref('')
const finishAtFilter = ref('')
const register1CAtFilter = ref('')
const statusFilter = ref(0)
const statusFilterType = ref('light')

// attract: Обработка выбора фильтра статуса
const filterByStatus = (item) => {
    statusFilter.value = item.id
    console.log(item)

    if (item.id === 0) {
        statusFilterType.value = 'light'
        return
    }

    const selectedStatus = Object.keys(FABRIC_ROLL_STATUS).find(
        (rollStatus) => FABRIC_ROLL_STATUS[rollStatus].CODE === item.id
    )

    statusFilterType.value = FABRIC_ROLL_STATUS[selectedStatus].TYPE
}

// attract: Сворачиваем/разворачиваем ПС
const toggleCollapse = (fabricGroup) => {
    fabricGroup.fabric.collapsed = !fabricGroup.fabric.collapsed
    localStorage.setItem(getFabricStorageName(fabricGroup.fabric.id), fabricGroup.fabric.collapsed) // сохраняем в локальное хранилище
    // console.log(fabricGroup)
}


// attract: Обработчик ввода номера рулона для фильтра
const handleRollNumberInput = (event) => {
    rollNumberFilter.value = event.target.value.replace(/[^0-9]/g, '')  // Оставляем только цифры (0-9)
}


// attract: Вспомогалочка: преобразует исходные данные в нужную структуру для отображения в шаблоне
const reformatData = (rolls, tabs) => {
    const activeTab = Object.values(tabs).find((tab) => tab.shown)
    doneRolls.value = getRollsByStatus(rolls, activeTab.code) // преобразуем в структуру для отображения
}

//attract: Применяем фильтры
const filtersApply = ({fabricFilter, rollNumberFilter, finishAtFilter, statusFilter, register1CAtFilter}) => {
    // prettier-ignore
    let filteredAllRolls = allRolls.value
        .filter(roll => roll.id.toString().includes(rollNumberFilter))
        .filter(roll => roll.fabric.display_name.toLowerCase().includes(fabricFilter.toLowerCase()))
        .filter(roll => getDateFromDateTimeString(roll.finish_at).includes(finishAtFilter))

    // prettier-ignore
    if ([FABRIC_ROLL_STATUS.DONE.CODE, FABRIC_ROLL_STATUS.REGISTERED_1C.CODE].includes(statusFilter)) {
        filteredAllRolls = filteredAllRolls.filter(roll => roll.status === statusFilter)
    }

    return filteredAllRolls
}

// attract: Следим за изменением активной вкладки
watch(
    () => tabs,
    (newActiveTabs) => {

        statusFilter.value = 0  // Сбрасываем фильтр статуса
        statusFilterType.value = 'light'

        // attract: Применяем фильтры, меняем doneRolls.value
        const filteredAllRolls = filtersApply({
            fabricFilter: fabricFilter.value,
            rollNumberFilter: rollNumberFilter.value,
            finishAtFilter: finishAtFilter.value,
            statusFilter: statusFilter.value,
        })

        reformatData(filteredAllRolls, newActiveTabs)
    },
    {deep: true /* immediate: true*/}
)

// attract: Следим за изменением самих данных (важны статусы после изменения/сохранения)
watch(
    () => allRolls.value,
    (newAllRolls) => {
        allRolls.value = newAllRolls

        // attract: Применяем фильтры, меняем doneRolls.value
        const filteredAllRolls = filtersApply({
            fabricFilter: fabricFilter.value,
            rollNumberFilter: rollNumberFilter.value,
            finishAtFilter: finishAtFilter.value,
            statusFilter: statusFilter.value,
        })

        reformatData(filteredAllRolls, tabs)
    },
    {deep: true /*immediate: true*/}
)

//attract: Реализация фильтров
watch(
    // prettier-ignore
    [
        () => fabricFilter.value,
        () => rollNumberFilter.value,
        () => finishAtFilter.value,
        () => statusFilter.value
    ],
    ([newFabricFilter, newRollNumberFilter, newFinishAtFilter, newStatusFilter]) => {
        const filteredAllRolls = filtersApply({
            fabricFilter: newFabricFilter,
            rollNumberFilter: newRollNumberFilter,
            finishAtFilter: newFinishAtFilter,
            statusFilter: newStatusFilter,
        })

        reformatData(filteredAllRolls, tabs) // attract: Применяем фильтры, меняем doneRolls.value
    }
)
</script>

<style scoped></style>
