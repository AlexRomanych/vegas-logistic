<template>

    <div v-if="!isLoading" class="ml-2 mt-2">

        <!-- __ Шапка таблицы -->
        <div class="sticky top-0 flex p-1 mb-1 bg-blue-200 border-2 rounded-lg border-blue-700 max-w-fit">
            <div>
                <!-- __ Название рисунка -->
                <AppLabelMultiLine
                    v-if="render.pictureName.show"
                    :align="render.pictureName.headerAlign"
                    :text-size="render.pictureName.headerTextSize"
                    :text="render.pictureName.header"
                    :type="typeof render.pictureName.type === 'function' ? render.pictureName.type(true) : render.pictureName.type"
                    :width="render.pictureName.width"
                    class="header-item"
                />

                <!-- __ Фильтр: Название рисунка -->
                <AppInputText
                    v-if="render.pictureName.show"
                    id="picture-name-search"
                    v-model.trim="nameFilter"
                    :text-size="render.pictureName.headerTextSize"
                    :width="render.pictureName.width"
                    height="h-[30px]"
                    placeholder="Рис."
                    type="primary"
                    @input="handlePictureNameInput"
                />
            </div>

            <div>
                <!-- __ Статус -->
                <AppLabelMultiLine
                    v-if="render.status.show"
                    :text="render.status.header"
                    :type="typeof render.status.type === 'function' ? render.status.type(true) : render.status.type"
                    :width="render.status.width"
                    :align="render.status.headerAlign"
                    :text-size="render.status.headerTextSize"
                    class="header-item"
                />

                <!-- Верстка  -->
                <div class="mt-2"></div>

                <!-- __ Фильтр: Статус -->
                <AppSelectSimple
                    :select-data="statusSelectData"
                    :width="render.status.width"
                    height="h-[30px]"
                    :text-size="render.status.headerTextSize"
                    type="primary"
                    @change="filterByStatus"
                />
            </div>

            <!-- __ Производительность -->
            <AppLabelMultiLine
                v-if="render.productivity.show"
                :text="render.productivity.header"
                :type="typeof render.productivity.type === 'function' ? render.productivity.type(true) : render.productivity.type"
                :width="render.productivity.width"
                :align="render.productivity.headerAlign"
                :text-size="render.productivity.headerTextSize"
                class="header-item"
            />

            <!-- __ Длина стежка -->
            <AppLabelMultiLine
                v-if="render.stitchLength.show"
                :text="render.stitchLength.header"
                :type="typeof render.stitchLength.type === 'function' ? render.stitchLength.type(true) : render.stitchLength.type"
                :width="render.stitchLength.width"
                :align="render.stitchLength.headerAlign"
                :text-size="render.stitchLength.headerTextSize"
                class="header-item"
            />

            <!-- __ Скорость стежков -->
            <AppLabelMultiLine
                v-if="render.stitchSpeed.show"
                :text="render.stitchSpeed.header"
                :type="typeof render.stitchSpeed.type === 'function' ? render.stitchSpeed.type(true) : render.stitchSpeed.type"
                :width="render.stitchSpeed.width"
                :align="render.stitchSpeed.headerAlign"
                :text-size="render.stitchSpeed.headerTextSize"
                class="header-item"
            />

            <!-- __ Мгновенная скорость -->
            <AppLabelMultiLine
                v-if="render.momentSpeed.show"
                :text="render.momentSpeed.header"
                :type="typeof render.momentSpeed.type === 'function' ? render.momentSpeed.type(true) : render.momentSpeed.type"
                :width="render.momentSpeed.width"
                :align="render.momentSpeed.headerAlign"
                :text-size="render.momentSpeed.headerTextSize"
                class="header-item"
            />

            <!-- __ Кол-во челноков (для корейца) -->
            <AppLabelMultiLine
                v-if="render.shuttleAmount.show"
                :text="render.shuttleAmount.header"
                :type="typeof render.shuttleAmount.type === 'function' ? render.shuttleAmount.type(true) : render.shuttleAmount.type"
                :width="render.shuttleAmount.width"
                :align="render.shuttleAmount.headerAlign"
                :text-size="render.shuttleAmount.headerTextSize"
                class="header-item"
            />

            <div>
                <!-- __ Основная СМ -->
                <AppLabelMultiLine
                    v-if="render.mainMachine.show"
                    :text="render.mainMachine.header"
                    :type="typeof render.mainMachine.type === 'function' ? render.mainMachine.type(true) : render.mainMachine.type"
                    :width="render.mainMachine.width"
                    :align="render.mainMachine.headerAlign"
                    :text-size="render.mainMachine.headerTextSize"
                    class="header-item"
                />

                <!-- Верстка  -->
                <div class="mt-2"></div>

                <!-- __ Фильтр: Основная СМ -->
                <AppSelectSimple
                    :select-data="mainMachine"
                    :type="typeof render.mainMachine.type === 'function' ? render.mainMachine.type(true) : render.mainMachine.type"
                    :width="render.mainMachine.width"
                    :text-size="render.mainMachine.headerTextSize"
                    @change="filterByMachine($event, 0)"
                />
            </div>

            <!-- __ Схема игл основной СМ -->
            <AppLabelMultiLine
                v-if="render.mainMachineSchema.show"
                :text="render.mainMachineSchema.header"
                :type="typeof render.mainMachineSchema.type === 'function' ? render.mainMachineSchema.type(true) : render.mainMachineSchema.type"
                :width="render.mainMachineSchema.width"
                :align="render.mainMachineSchema.headerAlign"
                :text-size="render.mainMachineSchema.headerTextSize"
                class="header-item"
            />

            <div>
                <!-- __ Альтернативная СМ 1 -->
                <AppLabelMultiLine
                    v-if="render.fabricAltMachine_1.show"
                    :text="render.fabricAltMachine_1.header"
                    :type="typeof render.fabricAltMachine_1.type === 'function' ? render.fabricAltMachine_1.type(true) : render.fabricAltMachine_1.type"
                    :width="render.fabricAltMachine_1.width"
                    :align="render.fabricAltMachine_1.headerAlign"
                    :text-size="render.fabricAltMachine_1.headerTextSize"
                    class="header-item"
                />

                <!-- Верстка  -->
                <div class="mt-2"></div>

                <!-- __ Фильтр: Альтернативная СМ 1 -->
                <AppSelectSimple
                    :select-data="mainMachine"
                    :type="typeof render.fabricAltMachine_1.type === 'function' ? render.fabricAltMachine_1.type(true) : render.fabricAltMachine_1.type"
                    :width="render.fabricAltMachine_1.width"
                    :text-size="render.fabricAltMachine_1.headerTextSize"
                    @change="filterByMachine($event, 1)"
                />
            </div>

            <!-- __ Схема игл Альтернативной СМ 1 -->
            <AppLabelMultiLine
                v-if="render.fabricAltMachineSchema_1.show"
                :text="render.fabricAltMachineSchema_1.header"
                :type="typeof render.fabricAltMachineSchema_1.type === 'function' ? render.fabricAltMachineSchema_1.type(true) : render.fabricAltMachineSchema_1.type"
                :width="render.fabricAltMachineSchema_1.width"
                :align="render.fabricAltMachineSchema_1.headerAlign"
                :text-size="render.fabricAltMachineSchema_1.headerTextSize"
                class="header-item"
            />

            <div>
                <!-- __ Альтернативная СМ 2 -->
                <AppLabelMultiLine
                    v-if="render.fabricAltMachine_2.show"
                    :text="render.fabricAltMachine_2.header"
                    :type="typeof render.fabricAltMachine_2.type === 'function' ? render.fabricAltMachine_2.type(true) : render.fabricAltMachine_2.type"
                    :width="render.fabricAltMachine_2.width"
                    :align="render.fabricAltMachine_2.headerAlign"
                    :text-size="render.fabricAltMachine_2.headerTextSize"
                    class="header-item"
                />

                <!-- Верстка  -->
                <div class="mt-2"></div>

                <!-- __ Фильтр: Альтернативная СМ 2 -->
                <AppSelectSimple
                    :select-data="mainMachine"
                    :type="typeof render.fabricAltMachine_2.type === 'function' ? render.fabricAltMachine_2.type(true) : render.fabricAltMachine_2.type"
                    :width="render.fabricAltMachine_2.width"
                    :text-size="render.fabricAltMachine_2.headerTextSize"
                    @change="filterByMachine($event, 2)"
                />
            </div>

            <!-- __ Схема игл Альтернативной СМ 2 -->
            <AppLabelMultiLine
                v-if="render.fabricAltMachineSchema_2.show"
                :text="render.fabricAltMachineSchema_2.header"
                :type="typeof render.fabricAltMachineSchema_2.type === 'function' ? render.fabricAltMachineSchema_2.type(true) : render.fabricAltMachineSchema_2.type"
                :width="render.fabricAltMachineSchema_2.width"
                :align="render.fabricAltMachineSchema_2.headerAlign"
                :text-size="render.fabricAltMachineSchema_2.headerTextSize"
                class="header-item"
            />

            <!-- __ Описание -->
            <AppLabelMultiLine
                v-if="render.description.show"
                :text="render.description.header"
                :type="typeof render.description.type === 'function' ? render.description.type(true) : render.description.type"
                :width="render.description.width"
                :align="render.description.headerAlign"
                :text-size="render.description.headerTextSize"
                class="header-item"
            />

            <!-- __ Кнопка 'Создать' -->
            <router-link :to="{ name: 'manufacture.cell.fabrics.picture.create' }">
                <AppLabelMultiLine
                    :text="['Создать', 'рисунок']"
                    align="center"
                    class="cursor-pointer"
                    text-size="mini"
                    type="success"
                    width="w-[90px]"
                />
            </router-link>

        </div>

        <div
            v-if="fabricPictures.length"
            class="p-1 mb-1 bg-blue-100 border-2 rounded-lg border-blue-600 max-w-fit"
        >
            <div
                v-for="fabricPicture in fabricPictures"
                :key="fabricPicture.id"
                class="flex"
            >
                <!-- __ № рулона  -->
                <AppLabel
                    v-if="render.pictureName.show"
                    :text="render.pictureName.data!(fabricPicture)"
                    :type="typeof render.pictureName.type === 'function' ? render.pictureName.type(false, fabricPicture) : render.pictureName.type"
                    :width="render.pictureName.width"
                    :align="render.pictureName.dataAlign"
                    :text-size="render.pictureName.dataTextSize"
                />

                <!-- __ № Статус -->
                <AppLabel
                    v-if="render.status.show"
                    :text="render.status.data!(fabricPicture)"
                    :type="typeof render.status.type === 'function' ? render.status.type(false, fabricPicture) : render.status.type"
                    :width="render.status.width"
                    :align="render.status.dataAlign"
                    :text-size="render.status.dataTextSize"
                />

                <!-- __ Производительность -->
                <AppLabel
                    v-if="render.productivity.show"
                    :text="render.productivity.data!(fabricPicture)"
                    :type="typeof render.productivity.type === 'function' ? render.productivity.type(false, fabricPicture) : render.productivity.type"
                    :width="render.productivity.width"
                    :align="render.productivity.dataAlign"
                    :text-size="render.productivity.dataTextSize"
                />

                <!-- __ Длина стежка -->
                <AppLabel
                    v-if="render.stitchLength.show"
                    :text="render.stitchLength.data!(fabricPicture)"
                    :type="typeof render.stitchLength.type === 'function' ? render.stitchLength.type(false, fabricPicture) : render.stitchLength.type"
                    :width="render.stitchLength.width"
                    :align="render.stitchLength.dataAlign"
                    :text-size="render.stitchLength.dataTextSize"
                />

                <!-- __ Скорость стежков -->
                <AppLabel
                    v-if="render.stitchSpeed.show"
                    :text="render.stitchSpeed.data!(fabricPicture)"
                    :type="typeof render.stitchSpeed.type === 'function' ? render.stitchSpeed.type(false, fabricPicture) : render.stitchSpeed.type"
                    :width="render.stitchSpeed.width"
                    :align="render.stitchSpeed.dataAlign"
                    :text-size="render.stitchSpeed.dataTextSize"
                />

                <!-- __ Мгновенная скорость -->
                <AppLabel
                    v-if="render.momentSpeed.show"
                    :text="render.momentSpeed.data!(fabricPicture)"
                    :type="typeof render.momentSpeed.type === 'function' ? render.momentSpeed.type(false, fabricPicture) : render.momentSpeed.type"
                    :width="render.momentSpeed.width"
                    :align="render.momentSpeed.dataAlign"
                    :text-size="render.momentSpeed.dataTextSize"
                />

                <!-- __ Кол-во челноков (для корейца) -->
                <AppLabel
                    v-if="render.shuttleAmount.show"
                    :text="render.shuttleAmount.data!(fabricPicture)"
                    :type="typeof render.shuttleAmount.type === 'function' ? render.shuttleAmount.type(false, fabricPicture) : render.shuttleAmount.type"
                    :width="render.shuttleAmount.width"
                    :align="render.shuttleAmount.dataAlign"
                    :text-size="render.shuttleAmount.dataTextSize"
                />

                <!-- __ Основная СМ -->
                <AppLabel
                    v-if="render.mainMachine.show"
                    :text="render.mainMachine.data!(fabricPicture)"
                    :type="typeof render.mainMachine.type === 'function' ? render.mainMachine.type(true) : render.mainMachine.type"
                    :width="render.mainMachine.width"
                    :align="render.mainMachine.dataAlign"
                    :text-size="render.mainMachine.dataTextSize"
                />

                <!-- __ Схема игл основной СМ -->
                <AppLabel
                    v-if="render.mainMachineSchema.show"
                    :text="render.mainMachineSchema.data!(fabricPicture)"
                    :type="typeof render.mainMachineSchema.type === 'function' ? render.mainMachineSchema.type(false, fabricPicture) : render.mainMachineSchema.type"
                    :width="render.mainMachineSchema.width"
                    :align="render.mainMachineSchema.dataAlign"
                    :text-size="render.mainMachineSchema.dataTextSize"
                />

                <!-- __ Альтернативная СМ 1 -->
                <AppLabel
                    v-if="render.fabricAltMachine_1.show"
                    :text="render.fabricAltMachine_1.data!(fabricPicture)"
                    :type="typeof render.fabricAltMachine_1.type === 'function' ? render.fabricAltMachine_1.type(false, fabricPicture) : render.fabricAltMachine_1.type"
                    :width="render.fabricAltMachine_1.width"
                    :align="render.fabricAltMachine_1.dataAlign"
                    :text-size="render.fabricAltMachine_1.dataTextSize"
                />

                <!-- __ Схема игл Альтернативной СМ 1 -->
                <AppLabel
                    v-if="render.fabricAltMachineSchema_1.show"
                    :text="render.fabricAltMachineSchema_1.data!(fabricPicture)"
                    :type="typeof render.fabricAltMachineSchema_1.type === 'function' ? render.fabricAltMachineSchema_1.type(false, fabricPicture) : render.fabricAltMachineSchema_1.type"
                    :width="render.fabricAltMachineSchema_1.width"
                    :align="render.fabricAltMachineSchema_1.dataAlign"
                    :text-size="render.fabricAltMachineSchema_1.dataTextSize"
                />

                <!-- __ Альтернативная СМ 2 -->
                <AppLabel
                    v-if="render.fabricAltMachine_2.show"
                    :text="render.fabricAltMachine_2.data!(fabricPicture)"
                    :type="typeof render.fabricAltMachine_2.type === 'function' ? render.fabricAltMachine_2.type(false, fabricPicture) : render.fabricAltMachine_2.type"
                    :width="render.fabricAltMachine_2.width"
                    :align="render.fabricAltMachine_2.dataAlign"
                    :text-size="render.fabricAltMachine_2.dataTextSize"
                />

                <!-- __ Схема игл Альтернативной СМ 2 -->
                <AppLabel
                    v-if="render.fabricAltMachineSchema_2.show"
                    :text="render.fabricAltMachineSchema_2.data!(fabricPicture)"
                    :type="typeof render.fabricAltMachineSchema_2.type === 'function' ? render.fabricAltMachineSchema_2.type(false, fabricPicture) : render.fabricAltMachineSchema_2.type"
                    :width="render.fabricAltMachineSchema_2.width"
                    :align="render.fabricAltMachineSchema_2.dataAlign"
                    :text-size="render.fabricAltMachineSchema_2.dataTextSize"
                />

                <!-- __ Описание -->
                <AppLabel
                    v-if="render.description.show"
                    :text="render.description.data!(fabricPicture)"
                    :type="typeof render.description.type === 'function' ? render.description.type(false, fabricPicture) : render.description.type"
                    :width="render.description.width"
                    :align="render.description.dataAlign"
                    :text-size="render.description.dataTextSize"
                />

                <!-- __ Кнопка 'Редактировать' -->
                <router-link :to="{ name: 'manufacture.cell.fabrics.picture.edit', params: { id: fabricPicture.id } }">
                    <AppLabel
                        align="center"
                        class="cursor-pointer"
                        text="Редактировать"
                        text-size="micro"
                        type="warning"
                        width="w-[90px]"
                    />
                </router-link>


            </div>

        </div>

        <div v-else>
            <div class="mt-5">

                <!-- __ Данные не найдены... -->
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

</template>

<script lang="ts" setup>

import { onMounted, reactive, ref, watch } from 'vue'

import type { IFabricPictureItem, IRenderData, ISelectData, ISelectDataItem } from '@/types'

import { FABRIC_MACHINES } from '@/app/constants/fabrics.js'

import { useFabricsStore } from '@/stores/FabricsStore.js'
import AppInputText from '@/components/ui/inputs/AppInputText.vue'
import AppLabelMultiLine from '@/components/ui/labels/AppLabelMultiLine.vue'
import AppLabel from '@/components/ui/labels/AppLabel.vue'
import AppSelectSimple from '@/components/ui/selects/AppSelectSimple.vue'

import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'

const fabricsStore = useFabricsStore()

const isLoading = ref(false)

// __ Подготавливаем переменные
let allFabricPictures: IFabricPictureItem[] = []
const fabricPictures = ref<IFabricPictureItem[]>([])


// __ Задаем глобальный объект для унификации отображения рулонов
const PRECISION = 2
const HEADER_ALIGN = 'center'
const DATA_ALIGN = 'center'
const HEADER_TEXT_SIZE = 'mini'
const DATA_TEXT_SIZE = 'micro'
// const getTypeOfRoll = (rollStatus, flag = false) => (flag ? 'primary' : getTypeByRollStatus(rollStatus))
const render: IRenderData = reactive({
    pictureName: {
        header: ['Название', 'рисунка'],
        width: 'w-[80px]',
        show: true,
        title: 'Название рисунка',
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        type: (flag = false, fabricPicture) => 'primary',
        // type: (flag = false, roll) => getTypeOfRoll(roll?.status, flag),
        // type: 'primary',
        data: (fabricPicture) => fabricPicture.name,
    },
    status: {
        header: ['Статус', 'рисунка'],
        width: 'w-[80px]',
        show: true,
        title: 'Статус рисунка',
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        type: (flag = false, fabricPicture) => (flag ? 'primary' : fabricPicture.active ? 'primary' : 'stone'),
        data: (fabricPicture) => fabricPicture.active ? 'Активный' : 'Архив',
    },
    productivity: {
        header: ['Произв-сть', 'м.п./час'],
        width: 'w-[120px]',
        show: true,
        title: 'Длина стежка, мм',
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        type: (flag = false, fabricPicture) => 'primary',
        data: (fabricPicture) => fabricPicture.productivity.toFixed(PRECISION),
    },
    stitchLength: {
        header: ['Длина', 'стежка, мм'],
        width: 'w-[80px]',
        show: true,
        title: 'Длина стежка, мм',
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        type: (flag = false, fabricPicture) => 'primary',
        data: (fabricPicture) => fabricPicture.stitch_length.toString(),
    },
    stitchSpeed: {
        header: ['Скорость', 'стежков, шт./мин'],
        width: 'w-[120px]',
        show: true,
        title: 'Скорость стежков, шт./мин',
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        type: (flag = false, fabricPicture) => 'primary',
        data: (fabricPicture) => fabricPicture.stitch_speed.toString(),
    },
    momentSpeed: {
        header: ['Мгновенная', 'скорость, м/час'],
        width: 'w-[120px]',
        show: true,
        title: 'Мгновенная скорость, м/час',
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        type: (flag = false, fabricPicture) => 'primary',
        data: (fabricPicture) => fabricPicture.moment_speed.toString(),
    },
    shuttleAmount: {
        header: ['Кол-во челноков', 'для Корейца, шт.'],
        width: 'w-[120px]',
        show: true,
        title: 'Мгновенная скорость, м/час',
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        type: (flag = false, fabricPicture) => 'primary',
        data: (fabricPicture) => fabricPicture.shuttle_amount !== null ? fabricPicture.shuttle_amount.toString() : '--',
    },
    mainMachine: {
        header: ['Основная', 'СМ'],
        width: 'w-[90px]',
        show: true,
        title: 'Основная Стегальная машина',
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        type: (flag = false, fabricPicture) => flag ? 'success' : fabricPicture?.machines.fabricMainMachine.machine.id ? 'success' : 'dark',
        data: (fabricPicture) => fabricPicture.machines.fabricMainMachine.machine.id ? fabricPicture.machines.fabricMainMachine.machine.short_name : '',
    },
    mainMachineSchema: {
        header: ['Схема игл', 'основной СМ'],
        width: 'w-[90px]',
        show: true,
        title: 'Схема игл основной СМ',
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        type: (flag = false, fabricPicture) => flag ? 'success' : fabricPicture?.machines.fabricMainMachine.schema.id ? 'success' : 'dark',
        data: (fabricPicture) => fabricPicture.machines.fabricMainMachine.schema.id ? fabricPicture.machines.fabricMainMachine.schema.schema : '',
    },
    fabricAltMachine_1: {
        header: ['Альтернат.', 'СМ 1'],
        width: 'w-[90px]',
        show: true,
        title: 'Альтернат. СМ 1',
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        type: (flag = false, fabricPicture) => flag ? 'indigo' : fabricPicture?.machines.fabricAltMachine_1.machine.id ? 'indigo' : 'dark',
        data: (fabricPicture) => fabricPicture.machines.fabricAltMachine_1.machine.id ? fabricPicture.machines.fabricAltMachine_1.machine.short_name : '',
    },
    fabricAltMachineSchema_1: {
        header: ['Схема игл', 'альтерн. СМ 1'],
        width: 'w-[90px]',
        show: true,
        title: 'Схема игл альтернативной. СМ 1',
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        type: (flag = false, fabricPicture) => flag ? 'indigo' : fabricPicture?.machines.fabricAltMachine_1.schema.id ? 'indigo' : 'dark',
        data: (fabricPicture) => fabricPicture.machines.fabricAltMachine_1.schema.id ? fabricPicture.machines.fabricAltMachine_1.schema.schema : '',
    },
    fabricAltMachine_2: {
        header: ['Альтернат.', 'СМ 2'],
        width: 'w-[90px]',
        show: true,
        title: 'Альтернат. СМ 1',
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        type: (flag = false, fabricPicture) => flag ? 'warning' : fabricPicture?.machines.fabricAltMachine_2.machine.id ? 'warning' : 'dark',
        data: (fabricPicture) => fabricPicture.machines.fabricAltMachine_2.machine.id ? fabricPicture.machines.fabricAltMachine_2.machine.short_name : '',
    },
    fabricAltMachineSchema_2: {
        header: ['Схема игл', 'альтерн. СМ 2'],
        width: 'w-[90px]',
        show: true,
        title: 'Схема игл альтернативной. СМ 2',
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        type: (flag = false, fabricPicture) => flag ? 'warning' : fabricPicture?.machines.fabricAltMachine_2.schema.id ? 'warning' : 'dark',
        data: (fabricPicture) => fabricPicture.machines.fabricAltMachine_2.schema.id ? fabricPicture.machines.fabricAltMachine_2.schema.schema : '',
    },
    description: {
        header: ['Описание', 'рисунка'],
        width: 'w-[200px]',
        show: true,
        title: 'Описание рисунка',
        headerAlign: HEADER_ALIGN,
        dataAlign: DATA_ALIGN,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize: DATA_TEXT_SIZE,
        type: (flag = false, fabricPicture) => 'primary',
        data: (fabricPicture) => fabricPicture.description,

    }
})


// __ Фильтры
const nameFilter = ref('')
const statusFilter = ref(1)
const mainMachineFilter = ref(0)
const altMachineFilter_1 = ref(0)
const altMachineFilter_2 = ref(0)

// __ Селект для статуса
const statusSelectData: ISelectData = {
    name: 'status',
    data: [
        {id: 1, name: 'Все', selected: true, disabled: false},
        {id: 2, name: 'Активный', selected: true, disabled: false},
        {id: 3, name: 'Архив', selected: true, disabled: false},
    ],
}

// __ Селекты для фильтрации СМ
const machinesData: ISelectDataItem[] = [
    {id: 0, name: 'Все', selected: true, disabled: false},
    {id: FABRIC_MACHINES.AMERICAN.ID, name: FABRIC_MACHINES.AMERICAN.NAME, selected: false, disabled: false},
    {id: FABRIC_MACHINES.GERMAN.ID, name: FABRIC_MACHINES.GERMAN.NAME, selected: false, disabled: false},
    {id: FABRIC_MACHINES.CHINA.ID, name: FABRIC_MACHINES.CHINA.NAME, selected: false, disabled: false},
    {id: FABRIC_MACHINES.KOREAN.ID, name: FABRIC_MACHINES.KOREAN.NAME, selected: false, disabled: false},
]

const mainMachine: ISelectData = {name: 'mainMachine', data: machinesData}


// __ Получаем все рисунки
const getFabricPictures = async () => {
    const pics: IFabricPictureItem[] = await fabricsStore.getFabricPictures()
    allFabricPictures = pics
        .filter((fabricPicture) => fabricPicture.id !== 0)
        .sort((a, b) => a.name.localeCompare(b.name))
    fabricPictures.value = allFabricPictures
    // return pics
    //     .filter((fabricPicture) => fabricPicture.id !== 0)
    //     .sort((a, b) => a.name.localeCompare(b.name))
}


// __ Меняем статус
const filterByStatus = (status: ISelectDataItem) => {
    statusFilter.value = status.id
}


// __ Меняем СМ
const filterByMachine = (event: ISelectDataItem, machineOrder: number /* 0 - основная СМ, 1 - альт СМ 1, 2 - альт СМ 2 */) => {
    switch (machineOrder) {
        case 0:
            mainMachineFilter.value = event.id
            break
        case 1:
            altMachineFilter_1.value = event.id
            break
        case 2:
            altMachineFilter_2.value = event.id
            break
    }
}


const handlePictureNameInput = () => {
}


// __ Реализация фильтра
watch(
    [
        () => nameFilter.value,
        () => statusFilter.value,
        () => mainMachineFilter.value,
        () => altMachineFilter_1.value,
        () => altMachineFilter_2.value,
    ],
    ([
         newNameFilter,
         newStatusFilter,
         newMainMachineFilter,
         newAltMachineFilter_1,
         newAltMachineFilter_2,
     ]) => {

        fabricPictures.value = allFabricPictures
        fabricPictures.value = fabricPictures.value.filter((picture) => picture.name.toLowerCase().includes(newNameFilter.toLowerCase()))

        // __ Фильтр по статусу
        if (newStatusFilter === 2 || newStatusFilter === 3) {
            newStatusFilter === 2
                ? (fabricPictures.value = fabricPictures.value.filter((picture) => picture.active))
                : (fabricPictures.value = fabricPictures.value.filter((picture) => !picture.active))
        }

        // __ Фильтр по основной СМ
        if (newMainMachineFilter !== 0) {
            fabricPictures.value = fabricPictures.value.filter((picture) => picture.machines.fabricMainMachine.machine.id === newMainMachineFilter)
        }

        // __ Фильтр по альт. СМ 1
        if (newAltMachineFilter_1 !== 0) {
            fabricPictures.value = fabricPictures.value.filter((picture) => picture.machines.fabricAltMachine_1.machine.id === newAltMachineFilter_1)
        }

        // __ Фильтр по альт. СМ 2
        if (newAltMachineFilter_2 !== 0) {
            fabricPictures.value = fabricPictures.value.filter((picture) => picture.machines.fabricAltMachine_2.machine.id === newAltMachineFilter_2)
        }
    },
    {deep: true}
)


onMounted(async () => {
    isLoading.value = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {
            await getFabricPictures()
            // console.log('fabricPictures: ', fabricPictures.value)
        },
        undefined,
        // false,
    )
    isLoading.value = false
})


</script>

<style scoped>

</style>
