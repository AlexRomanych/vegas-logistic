<template>
    <template v-if="!isLoading">
        <div class="mx-0.5">
            <div class="flex">
                <!-- __ Collapsed -->
                <AppLabelMultilineTSWrapper :render-object="render.collapsed" @click="toggleCollapsed"/>

                <!-- __ Название СЗ -->
                <AppLabelMultilineTSWrapper :render-object="render.task_title" @click="toggleCollapsed"/>

                <!-- __ Всего, Площадь -->
                <AppLabelMultilineTSWrapper :render-object="render.task_total_square" @click="toggleCollapsed"/>

                <!-- __ Всего, Количество -->
                <AppLabelMultilineTSWrapper :render-object="render.task_total_pics" @click="toggleCollapsed"/>

                <!-- __ Всего, Трудозатраты -->
                <AppLabelMultilineTSWrapper :render-object="render.task_total_time" @click="toggleCollapsed"/>

                <!-- __ Выполнено, Площадь -->
                <AppLabelMultilineTSWrapper :render-object="render.task_total_done_square" @click="toggleCollapsed"/>

                <!-- __ Выполнено, Количество -->
                <AppLabelMultilineTSWrapper :render-object="render.task_total_done_pics" @click="toggleCollapsed"/>

                <!-- __ Выполнено, Трудозатраты -->
                <AppLabelMultilineTSWrapper :render-object="render.task_total_done_time" @click="toggleCollapsed"/>

                <!-- __ Не Выполнено, Площадь -->
                <AppLabelMultilineTSWrapper :render-object="render.task_total_false_square" @click="toggleCollapsed"/>

                <!-- __ Не Выполнено, Количество -->
                <AppLabelMultilineTSWrapper :render-object="render.task_total_false_pics" @click="toggleCollapsed"/>

                <!-- __ Не Выполнено, Трудозатраты -->
                <AppLabelMultilineTSWrapper :render-object="render.task_total_false_time" @click="toggleCollapsed"/>

            </div>

            <!-- __ Данные (Модели) -->
            <div
                v-for="dataObject of dataObjects"
                :key="dataObject.taskTitle"
                class="max-w-fit"

            >
                <div class="flex">

                    <!-- __ Collapsed -->
                    <AppLabelTSWrapper
                        :arg="dataObject.collapsed"
                        :render-object="render.collapsed"
                        class="cursor-pointer"
                        @click="toggleCollapsedTask(dataObject)"
                    />

                    <!-- __ Название -->
                    <AppLabelTSWrapper
                        :arg="dataObject"
                        :render-object="render.task_title"
                        class="cursor-pointer"
                        @click="toggleCollapsedTask(dataObject)"
                    />

                    <!-- __ Всего по СЗ, Площадь -->
                    <AppLabelTSWrapper
                        :arg="dataObject"
                        :render-object="render.task_total_square"
                        class="cursor-pointer"
                        @click="toggleCollapsedTask(dataObject)"
                    />

                    <!-- __ Всего по СЗ, Количество -->
                    <AppLabelTSWrapper
                        :arg="dataObject"
                        :render-object="render.task_total_pics"
                        class="cursor-pointer"
                        @click="toggleCollapsedTask(dataObject)"
                    />

                    <!-- __ Всего по СЗ, Трудозатраты -->
                    <AppLabelTSWrapper
                        :arg="dataObject"
                        :render-object="render.task_total_time"
                        class="cursor-pointer"
                        @click="toggleCollapsedTask(dataObject)"
                    />

                    <!-- __ Выполнено, Площадь -->
                    <AppLabelTSWrapper
                        :arg="dataObject"
                        :render-object="render.task_total_done_square"
                        class="cursor-pointer"
                        @click="toggleCollapsedTask(dataObject)"
                    />

                    <!-- __ Выполнено, Количество -->
                    <AppLabelTSWrapper
                        :arg="dataObject"
                        :render-object="render.task_total_done_pics"
                        class="cursor-pointer"
                        @click="toggleCollapsedTask(dataObject)"
                    />

                    <!-- __ Выполнено, Трудозатраты -->
                    <AppLabelTSWrapper
                        :arg="dataObject"
                        :render-object="render.task_total_done_time"
                        class="cursor-pointer"
                        @click="toggleCollapsedTask(dataObject)"
                    />

                    <!-- __ Не Выполнено, Площадь -->
                    <AppLabelTSWrapper
                        :arg="dataObject"
                        :render-object="render.task_total_false_square"
                        class="cursor-pointer"
                        @click="toggleCollapsedTask(dataObject)"
                    />

                    <!-- __ Не Выполнено, Количество -->
                    <AppLabelTSWrapper
                        :arg="dataObject"
                        :render-object="render.task_total_false_pics"
                        class="cursor-pointer"
                        @click="toggleCollapsedTask(dataObject)"
                    />

                    <!-- __ Не Выполнено, Трудозатраты -->
                    <AppLabelTSWrapper
                        :arg="dataObject"
                        :render-object="render.task_total_false_time"
                        class="cursor-pointer"
                        @click="toggleCollapsedTask(dataObject)"
                    />

                </div>

                <!-- __ Группы Производственных Линий (Линия 1 и Линия 2) -->
                <template v-if="!dataObject.collapsed">

                    <div v-for="(group, index) of dataObject.groups" :key="index" class="ml-[34px]">
                        <template v-if="group.hasData">

                            <div class="flex">

                                <!-- __ Collapsed -->
                                <AppLabelTSWrapper
                                    :arg="group.collapsed"
                                    :render-object="render.collapsed"
                                    class="cursor-pointer"
                                    @click="toggleCollapsedGroup(group)"
                                />

                                <!-- __ Название группы -->
                                <AppLabelTSWrapper
                                    :arg="group"
                                    :render-object="render.group_title"
                                    class="cursor-pointer"
                                    @click="toggleCollapsedGroup(group)"
                                />

                                <!-- __ Всего по Площади -->
                                <AppLabelTSWrapper
                                    :arg="group"
                                    :render-object="render.group_total_square"
                                    class="cursor-pointer"
                                    @click="toggleCollapsedGroup(group)"
                                />

                                <!-- __ Всего по Количеству -->
                                <AppLabelTSWrapper
                                    :arg="group"
                                    :render-object="render.group_total_pics"
                                    class="cursor-pointer"
                                    @click="toggleCollapsedGroup(group)"
                                />

                                <!-- __ Всего по Трудозатратам -->
                                <AppLabelTSWrapper
                                    :arg="group"
                                    :render-object="render.group_total_time"
                                    class="cursor-pointer"
                                    @click="toggleCollapsedGroup(group)"
                                />

                                <!-- __ Выполнено по Площади -->
                                <AppLabelTSWrapper
                                    :arg="group"
                                    :render-object="render.group_done_square"
                                    class="cursor-pointer"
                                    @click="toggleCollapsedGroup(group)"
                                />

                                <!-- __ Выполнено по Количеству -->
                                <AppLabelTSWrapper
                                    :arg="group"
                                    :render-object="render.group_done_pics"
                                    class="cursor-pointer"
                                    @click="toggleCollapsedGroup(group)"
                                />

                                <!-- __ Выполнено по Трудозатратам -->
                                <AppLabelTSWrapper
                                    :arg="group"
                                    :render-object="render.group_done_time"
                                    class="cursor-pointer"
                                    @click="toggleCollapsedGroup(group)"
                                />

                                <!-- __ Не Выполнено по Площади -->
                                <AppLabelTSWrapper
                                    :arg="group"
                                    :render-object="render.group_false_square"
                                    class="cursor-pointer"
                                    @click="toggleCollapsedGroup(group)"
                                />

                                <!-- __ Не Выполнено по Количеству -->
                                <AppLabelTSWrapper
                                    :arg="group"
                                    :render-object="render.group_false_pics"
                                    class="cursor-pointer"
                                    @click="toggleCollapsedGroup(group)"
                                />

                                <!-- __ Не Выполнено по Трудозатратам -->
                                <AppLabelTSWrapper
                                    :arg="group"
                                    :render-object="render.group_false_time"
                                    class="cursor-pointer"
                                    @click="toggleCollapsedGroup(group)"
                                />

                            </div>

                            <!-- __ Подгруппы (Коллекции Блоков) -->
                            <template v-if="!group.collapsed">

                                <div v-for="(subgroup, idx) of group.subgroups" :key="idx" class="ml-[19px]">

                                    <template v-if="subgroup.hasData">
                                        <div class="flex ml-[15px]">

                                            <!-- __ Название Подгруппы (Коллекции Блоков) -->
                                            <AppLabelTSWrapper
                                                :arg="subgroup"
                                                :render-object="render.subgroup_title"
                                            />

                                            <!-- __ Всего по подгруппе Площадь -->
                                            <AppLabelTSWrapper
                                                :arg="subgroup"
                                                :render-object="render.subgroup_total_square"
                                            />

                                            <!-- __ Всего по подгруппе Количество -->
                                            <AppLabelTSWrapper
                                                :arg="subgroup"
                                                :render-object="render.subgroup_total_pics"
                                            />

                                            <!-- __ Всего по подгруппе Трудозатраты -->
                                            <AppLabelTSWrapper
                                                :arg="subgroup"
                                                :render-object="render.subgroup_total_time"
                                            />

                                            <!-- __ Выполнено по подгруппе Площадь -->
                                            <AppLabelTSWrapper
                                                :arg="subgroup"
                                                :render-object="render.subgroup_done_square"
                                            />

                                            <!-- __ Выполнено по подгруппе Количество -->
                                            <AppLabelTSWrapper
                                                :arg="subgroup"
                                                :render-object="render.subgroup_done_pics"
                                            />

                                            <!-- __ Выполнено по подгруппе Трудозатраты -->
                                            <AppLabelTSWrapper
                                                :arg="subgroup"
                                                :render-object="render.subgroup_done_time"
                                            />

                                            <!-- __ Не Выполнено по подгруппе Площадь -->
                                            <AppLabelTSWrapper
                                                :arg="subgroup"
                                                :render-object="render.subgroup_false_square"
                                            />

                                            <!-- __ Не Выполнено по подгруппе Количество -->
                                            <AppLabelTSWrapper
                                                :arg="subgroup"
                                                :render-object="render.subgroup_false_pics"
                                            />

                                            <!-- __ Не Выполнено по подгруппе Трудозатраты -->
                                            <AppLabelTSWrapper
                                                :arg="subgroup"
                                                :render-object="render.subgroup_false_time"
                                            />

                                        </div>
                                    </template>
                                </div>

                            </template>

                        </template>
                    </div>

                </template>
            </div>
        </div>
    </template>
</template>

<script lang="ts" setup>
import { onMounted, reactive, ref, watch } from 'vue'
import type { IRenderData, IBlockDay, IBlockTaskLine, IBlockTaskLinesGroupData, IBlockTaskLinesSubgroup } from '@/types'

import { BLOCK_UNION_TASK_NAME } from '@/app/constants/blocks.ts'

import { getBlockTaskLineSquare, getBlockTaskLineTime, groupTaskLinesForExecute } from '@/app/helpers/manufacture/helpers_blocks.ts'
import { formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date'

import AppLabelMultilineTSWrapper from '@/components/dashboard/manufacture/cells/components/AppLabelMultilineTSWrapper.vue'
import AppLabelTSWrapper from '@/components/dashboard/manufacture/cells/components/AppLabelTSWrapper.vue'


interface IProps {
    blockDay: IBlockDay | null
}

interface IDataObject {
    taskTitle: string
    groups: IBlockTaskLinesGroupData[]
    collapsed: boolean
    id: number
}

type IEntity = IDataObject

const props = defineProps<IProps>()

const isLoading = ref(true)

// __ Данные
const dataObjects = ref<IDataObject[]>([])

// __ Объект отображения данных
const DEFAULT_HEIGHT   = 'h-[25px]'
const DEFAULT_WIDTH    = 'w-[100px]'
const HEADER_TYPE      = 'indigo'
const DATA_TYPE        = 'success'
const DEFAULT_TYPE     = 'dark'
const HEADER_TEXT_SIZE = 'mini'
const DATA_TEXT_SIZE   = 'mini'
const HEADER_ALIGN     = 'center'
const DATA_ALIGN       = 'center'
const DONE_TYPE        = 'success'
const FALSE_TYPE       = 'danger'
const TOTAL_TYPE       = 'primary'

const render: IRenderData = reactive({
    collapsed              : {
        header        : ['▲', '▼'],
        width         : 'w-[30px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => 'warning',
        dataType      : () => DATA_TYPE,
        type          : () => 'warning',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'center',
        data          : (state: boolean) => state ? '▲' : '▼',
        // data          : (collection: IModelCollection) => (collection.collapsed ? '▲' : '▼'),
        // click         : (collection: IModelCollection) => (collection.collapsed = !collection.collapsed),
        class: 'cursor-pointer',
    },
    task_title             : {
        header        : ['СМЕННОЕ', 'ЗАДАНИЕ'],
        width         : 'w-[348px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IEntity) => entity?.taskTitle === BLOCK_UNION_TASK_NAME ? 'info' : 'stone',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: IEntity) => entity?.taskTitle || '',
    },
    task_total_square      : {
        header        : ['ВСЕГО S,', 'КВ.М.'],
        width         : DEFAULT_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => TOTAL_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: IEntity) => groupSumSquare(entity.groups).toFixed(3)
    },
    task_total_pics        : {
        header        : ['ВСЕГО,', 'ШТ.'],
        width         : DEFAULT_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => TOTAL_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: IEntity) => groupSumPics(entity.groups).toString()
    },
    task_total_time        : {
        header        : ['ВСЕГО,', 'ТРУД-ТЫ'],
        width         : DEFAULT_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => TOTAL_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: IEntity) => formatTimeWithLeadingZeros(groupSumTime(entity.groups), 'hour'),
    },
    task_total_done_square : {
        header        : ['ВЫП-НО S,', 'КВ.М.'],
        width         : DEFAULT_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DONE_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: IEntity) => groupSumSquare(entity.groups, true).toFixed(3),
    },
    task_total_done_pics   : {
        header        : ['ВЫП-НО', 'ШТ.'],
        width         : DEFAULT_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DONE_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: IEntity) => groupSumPics(entity.groups, true).toString(),
    },
    task_total_done_time   : {
        header        : ['ВЫП-НО', 'ТРУД-ТЫ'],
        width         : DEFAULT_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DONE_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: IEntity) => formatTimeWithLeadingZeros(groupSumTime(entity.groups, true), 'hour'),
    },
    task_total_false_square: {
        header        : ['НЕ ВЫП-НО S,', 'КВ.М.'],
        width         : DEFAULT_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => FALSE_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: IEntity) => groupSumSquare(entity.groups, false).toFixed(3),
    },
    task_total_false_pics  : {
        header        : ['НЕ ВЫП-НО', 'ШТ.'],
        width         : DEFAULT_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => FALSE_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: IEntity) => groupSumPics(entity.groups, false).toString(),
    },
    task_total_false_time  : {
        header        : ['НЕ ВЫП-НО', 'ТРУД-ТЫ'],
        width         : DEFAULT_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => FALSE_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: IEntity) => formatTimeWithLeadingZeros(groupSumTime(entity.groups, false), 'hour'),
    },
    group_title            : {
        header        : ['СМЕННОЕ ЗАДАНИЕ', ''],
        width         : 'w-[314px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IBlockTaskLinesGroupData) => entity.groupType,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: IBlockTaskLinesGroupData) => entity.groupName,
    },
    group_total_square     : {
        header        : ['ВСЕГО', ''],
        width         : DEFAULT_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => TOTAL_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: IBlockTaskLinesGroupData) => subgroupSumSquare(entity.subgroups).toFixed(3),
    },
    group_total_pics       : {
        header        : ['ВСЕГО', ''],
        width         : DEFAULT_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => TOTAL_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: IBlockTaskLinesGroupData) => subgroupSumPics(entity.subgroups).toString(),
    },
    group_total_time       : {
        header        : ['ВСЕГО', ''],
        width         : DEFAULT_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => TOTAL_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: IBlockTaskLinesGroupData) => formatTimeWithLeadingZeros(subgroupSumTime(entity.subgroups), 'hour'),
    },
    group_done_square      : {
        header        : ['ВЫПОЛНЕНО', ''],
        width         : DEFAULT_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DONE_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: IBlockTaskLinesGroupData) => subgroupSumSquare(entity.subgroups, true).toFixed(3),
    },
    group_done_pics        : {
        header        : ['ВЫПОЛНЕНО', ''],
        width         : DEFAULT_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DONE_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: IBlockTaskLinesGroupData) => subgroupSumPics(entity.subgroups, true).toString(),
    },
    group_done_time        : {
        header        : ['ВЫПОЛНЕНО', ''],
        width         : DEFAULT_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DONE_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: IBlockTaskLinesGroupData) => formatTimeWithLeadingZeros(subgroupSumTime(entity.subgroups, true), 'hour'),
    },
    group_false_square     : {
        header        : ['НЕ ВЫПОЛНЕНО', ''],
        width         : DEFAULT_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => FALSE_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: IBlockTaskLinesGroupData) => subgroupSumSquare(entity.subgroups, false).toFixed(3),
    },
    group_false_pics       : {
        header        : ['НЕ ВЫПОЛНЕНО', ''],
        width         : DEFAULT_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => FALSE_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: IBlockTaskLinesGroupData) => subgroupSumPics(entity.subgroups, false).toString(),
    },
    group_false_time       : {
        header        : ['НЕ ВЫПОЛНЕНО', ''],
        width         : DEFAULT_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => FALSE_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: IBlockTaskLinesGroupData) => formatTimeWithLeadingZeros(subgroupSumTime(entity.subgroups, false), 'hour'),
    },
    subgroup_title         : {
        header        : ['СМЕННОЕ ЗАДАНИЕ', ''],
        width         : 'w-[315px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DEFAULT_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : 'left',
        data          : (entity: IBlockTaskLinesSubgroup) => entity.subgroupName,
    },
    subgroup_total_square  : {
        header        : ['ВСЕГО', ''],
        width         : DEFAULT_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => TOTAL_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: IBlockTaskLinesSubgroup) => linesSumSquare(entity.lines).toFixed(3),
    },
    subgroup_total_pics    : {
        header        : ['ВСЕГО', ''],
        width         : DEFAULT_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => TOTAL_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: IBlockTaskLinesSubgroup) => linesSumPics(entity.lines).toString(),
    },
    subgroup_total_time    : {
        header        : ['ВСЕГО', ''],
        width         : DEFAULT_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => TOTAL_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: IBlockTaskLinesSubgroup) => formatTimeWithLeadingZeros(linesSumTime(entity.lines), 'hour'),
    },
    subgroup_done_square   : {
        header        : ['ВЫПОЛНЕНО', ''],
        width         : DEFAULT_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DONE_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: IBlockTaskLinesSubgroup) => linesSumSquare(entity.lines, true).toFixed(3),
    },
    subgroup_done_pics     : {
        header        : ['ВЫПОЛНЕНО', ''],
        width         : DEFAULT_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DONE_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: IBlockTaskLinesSubgroup) => linesSumPics(entity.lines, true).toString(),
    },
    subgroup_done_time     : {
        header        : ['ВЫПОЛНЕНО', ''],
        width         : DEFAULT_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => DONE_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: IBlockTaskLinesSubgroup) => formatTimeWithLeadingZeros(linesSumTime(entity.lines, true), 'hour'),
    },
    subgroup_false_square  : {
        header        : ['НЕ ВЫПОЛНЕНО', ''],
        width         : DEFAULT_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => FALSE_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: IBlockTaskLinesSubgroup) => linesSumSquare(entity.lines, false).toFixed(3),
    },
    subgroup_false_pics    : {
        header        : ['НЕ ВЫПОЛНЕНО', ''],
        width         : DEFAULT_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => FALSE_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: IBlockTaskLinesSubgroup) => linesSumPics(entity.lines, false).toString(),
    },
    subgroup_false_time    : {
        header        : ['НЕ ВЫПОЛНЕНО', ''],
        width         : DEFAULT_WIDTH,
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : () => FALSE_TYPE,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: IBlockTaskLinesSubgroup) => formatTimeWithLeadingZeros(linesSumTime(entity.lines, false), 'hour'),
    },
})

// __ Сумма по подгруппе
const linesSumPics = (lines: IBlockTaskLine[] = [], checkType: boolean | null = null) => lines.reduce((accLines, line) => {
    if (checkType === null) return accLines + line.amount
    if (checkType) {
        return line.finished_at ? accLines + line.amount : accLines
    } else {
        return !line.finished_at ? accLines + line.amount : accLines

    }
}, 0)

const linesSumTime = (lines: IBlockTaskLine[] = [], checkType: boolean | null = null) => lines.reduce((accLines, line) => {
    if (checkType === null) return accLines + getBlockTaskLineTime(line)
    if (checkType) {
        return line.finished_at ? accLines + getBlockTaskLineTime(line) : accLines
    } else {
        return !line.finished_at ? accLines + getBlockTaskLineTime(line) : accLines
    }
}, 0)

const linesSumSquare = (lines: IBlockTaskLine[] = [], checkType: boolean | null = null) => lines.reduce((accLines, line) => {
    if (checkType === null) return accLines + getBlockTaskLineSquare(line)
    if (checkType) {
        return line.finished_at ? accLines + getBlockTaskLineSquare(line) : accLines
    } else {
        return !line.finished_at ? accLines + getBlockTaskLineSquare(line) : accLines
    }
}, 0)


// __ Сумма по группе
const subgroupSumPics   = (subgroups: IBlockTaskLinesSubgroup[] = [], checkType: boolean | null = null) => subgroups.reduce((accSubgroup, subgroup) => accSubgroup + linesSumPics(subgroup.lines, checkType), 0)
const subgroupSumTime   = (subgroups: IBlockTaskLinesSubgroup[] = [], checkType: boolean | null = null) => subgroups.reduce((accSubgroup, subgroup) => accSubgroup + linesSumTime(subgroup.lines, checkType), 0)
const subgroupSumSquare = (subgroups: IBlockTaskLinesSubgroup[] = [], checkType: boolean | null = null) => subgroups.reduce((accSubgroup, subgroup) => accSubgroup + linesSumSquare(subgroup.lines, checkType), 0)

// __ Сумма по объекту (СЗ)
const groupSumPics   = (groups: IBlockTaskLinesGroupData[] = [], checkType: boolean | null = null) => groups.reduce((accGroup, group) => accGroup + subgroupSumPics(group.subgroups, checkType), 0)
const groupSumTime   = (groups: IBlockTaskLinesGroupData[] = [], checkType: boolean | null = null) => groups.reduce((accGroup, group) => accGroup + subgroupSumTime(group.subgroups, checkType), 0)
const groupSumSquare = (groups: IBlockTaskLinesGroupData[] = [], checkType: boolean | null = null) => groups.reduce((accGroup, group) => accGroup + subgroupSumSquare(group.subgroups, checkType), 0)

// __ Сумма по всем объектам (СЗ + объединенное)
// const dataSumPics     = (dataObjectsList: IDataObject[] = dataObjects.value!, checkType: boolean | null = null) => dataObjectsList.reduce((accObject, dataObject) => accObject + groupSumPics(dataObject.groups, checkType), 0)
// const dataSumTime     = (dataObjectsList: IDataObject[] = dataObjects.value!, checkType: boolean | null = null) => dataObjectsList.reduce((accObject, dataObject) => accObject + groupSumTime(dataObject.groups, checkType), 0)


// __ Формируем объект с данными для отображения
const prepareDataObjects = (): IDataObject[] => {
    const COLLAPSED_STATE = true


    let resultObject: IDataObject[] = []
    if (!props.blockDay) {
        return []
    }
    let unionTasks: IBlockTaskLine[] = []
    props.blockDay.block_tasks.forEach((task, idx) => {
        unionTasks = [...unionTasks, ...task.block_lines]

        resultObject.push({
            taskTitle: `${task.position}. ${task.order.client.short_name} №${task.order.order_no_num}`,
            groups   : groupTaskLinesForExecute(task.block_lines),
            collapsed: COLLAPSED_STATE,
            id       : idx
        })
    })

    const resultObjectLength = resultObject.length

    resultObject.push({
        taskTitle: BLOCK_UNION_TASK_NAME,
        groups   : groupTaskLinesForExecute(unionTasks),
        collapsed: COLLAPSED_STATE,
        id       : resultObjectLength
    })

    resultObject = resultObject.map(dataObject => {
        return {
            ...dataObject,
            groups: dataObject.groups.map(group => ({ ...group, collapsed: COLLAPSED_STATE }))
        }
    })

    return resultObject
}


let totalCollapsedState = false
const toggleCollapsed   = () => {
    totalCollapsedState = !totalCollapsedState
    dataObjects.value!.forEach(dataObject => {
        dataObject.collapsed = totalCollapsedState
        dataObject.groups.forEach(group => group.collapsed = totalCollapsedState)
    })
}

//
const toggleCollapsedTask = (dataObject: IDataObject) => {
    dataObject.collapsed = !dataObject.collapsed
}

const toggleCollapsedGroup = (group: IBlockTaskLinesGroupData) => {
    group.collapsed = !group.collapsed
}

onMounted(() => {
    isLoading.value   = true
    dataObjects.value = prepareDataObjects()
    // console.log('data: ', dataObjects.value)
    isLoading.value   = false
})

watch(() => props.blockDay, () => {
    dataObjects.value = prepareDataObjects()
}, { deep: true, immediate: true })

</script>

<style scoped>

</style>

