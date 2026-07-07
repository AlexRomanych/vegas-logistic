<template>
    <template v-if="!isLoading">
        <div class="mx-0.5">
            <div class="flex">
                <!-- __ Collapsed -->
                <AppLabelMultilineTSWrapper :render-object="render.collapsed" @click="toggleCollapsed"/>

                <!-- __ Название СЗ -->
                <AppLabelMultilineTSWrapper :render-object="render.task_title" @click="toggleCollapsed"/>

                <!-- __ Всего -->
                <AppLabelMultilineTSWrapper :render-object="render.task_total" @click="toggleCollapsed"/>

                <!-- __ Выполнено -->
                <AppLabelMultilineTSWrapper :render-object="render.task_total_done" @click="toggleCollapsed"/>

                <!-- __ Не Выполнено -->
                <AppLabelMultilineTSWrapper :render-object="render.task_total_false" @click="toggleCollapsed"/>

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

                    <!-- __ Всего по СЗ -->
                    <AppLabelTSWrapper
                        :arg="dataObject"
                        :render-object="render.task_total"
                        class="cursor-pointer"
                        @click="toggleCollapsedTask(dataObject)"
                    />

                    <!-- __ Выполнено -->
                    <AppLabelTSWrapper
                        :arg="dataObject"
                        :render-object="render.task_total_done"
                        class="cursor-pointer"
                        @click="toggleCollapsedTask(dataObject)"
                    />

                    <!-- __ Не Выполнено -->
                    <AppLabelTSWrapper
                        :arg="dataObject"
                        :render-object="render.task_total_false"
                        class="cursor-pointer"
                        @click="toggleCollapsedTask(dataObject)"
                    />

                </div>

                <!-- __ Группы ШМ (АШМ, УШМ и Н/Д) -->
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

                                <!-- __ Всего по ШМ -->
                                <AppLabelTSWrapper
                                    :arg="group"
                                    :render-object="render.group_total"
                                    class="cursor-pointer"
                                    @click="toggleCollapsedGroup(group)"
                                />

                                <!-- __ Выполнено по ШМ -->
                                <AppLabelTSWrapper
                                    :arg="group"
                                    :render-object="render.group_done"
                                    class="cursor-pointer"
                                    @click="toggleCollapsedGroup(group)"
                                />

                                <!-- __ Не Выполнено по ШМ -->
                                <AppLabelTSWrapper
                                    :arg="group"
                                    :render-object="render.group_false"
                                    class="cursor-pointer"
                                    @click="toggleCollapsedGroup(group)"
                                />

                            </div>

                            <!-- __ Подгруппы (УШМ + окантователь, Глухие ...) -->
                            <template v-if="!group.collapsed">

                                <div v-for="(subgroup, idx) of group.subgroups" :key="idx" class="ml-[34px]">

                                    <template v-if="subgroup.hasData">
                                        <div class="flex ml-[15px]">

                                            <!-- __ Название Подгруппы (УШМ + окантователь, Глухие ...) -->
                                            <AppLabelTSWrapper
                                                :arg="subgroup"
                                                :render-object="render.subgroup_title"
                                            />

                                            <!-- __ Всего по подгруппе -->
                                            <AppLabelTSWrapper
                                                :arg="subgroup"
                                                :render-object="render.subgroup_total"
                                            />

                                            <!-- __ Выполнено по подгруппе -->
                                            <AppLabelTSWrapper
                                                :arg="subgroup"
                                                :render-object="render.subgroup_done"
                                            />

                                            <!-- __ Не Выполнено по подгруппе -->
                                            <AppLabelTSWrapper
                                                :arg="subgroup"
                                                :render-object="render.subgroup_false"
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
import type { IRenderData, ICuttingDay, ICuttingTaskLine, ICuttingTaskLinesGroupData, ICuttingTaskLinesSubgroup } from '@/types'

import { CUTTING_UNION_TASK_NAME } from '@/app/constants/cutting.ts'

import { getCuttingTaskLineTime, groupTaskLinesForExecute } from '@/app/helpers/manufacture/helpers_cutting.ts'

import AppLabelMultilineTSWrapper from '@/components/dashboard/manufacture/cells/components/AppLabelMultilineTSWrapper.vue'
import AppLabelTSWrapper from '@/components/dashboard/manufacture/cells/components/AppLabelTSWrapper.vue'
import { formatTimeWithLeadingZeros } from '@/app/helpers/helpers_date'

// import AppInputTextTSWrapper from '@/components/dashboard/manufacture/cells/components/AppInputTextTSWrapper.vue'

interface IProps {
    cuttingDay: ICuttingDay | null
}

interface IDataObject {
    taskTitle: string
    groups: ICuttingTaskLinesGroupData[]
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
const DEFAULT_WIDTH    = 'w-[200px]'
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
    collapsed       : {
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
    task_title      : {
        header        : ['СМЕННОЕ', 'ЗАДАНИЕ'],
        width         : 'w-[248px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: IEntity) => entity?.taskTitle === CUTTING_UNION_TASK_NAME ? 'info' : 'stone',
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: IEntity) => entity?.taskTitle || '',
    },
    task_total      : {
        header        : ['ВСЕГО,', 'ШТ. / ТР-ТЫ'],
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
        data          : (entity: IEntity) => groupSumPics(entity.groups).toString() + ' шт. / ' + formatTimeWithLeadingZeros(groupSumTime(entity.groups)),
    },
    task_total_done : {
        header        : ['ВЫПОЛНЕНО,', 'ШТ. / ТР-ТЫ'],
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
        data          : (entity: IEntity) => groupSumPics(entity.groups, true).toString() + ' шт. / ' + formatTimeWithLeadingZeros(groupSumTime(entity.groups, true)),
    },
    task_total_false: {
        header        : ['НЕ ВЫПОЛНЕНО,', 'ШТ. / ТР-ТЫ'],
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
        data          : (entity: IEntity) => groupSumPics(entity.groups, false).toString() + ' шт. / ' + formatTimeWithLeadingZeros(groupSumTime(entity.groups, false)),
    },
    group_title     : {
        header        : ['СМЕННОЕ ЗАДАНИЕ', ''],
        width         : 'w-[214px]',
        height        : DEFAULT_HEIGHT,
        show          : true,
        headerType    : () => HEADER_TYPE,
        dataType      : () => DATA_TYPE,
        type          : (entity: ICuttingTaskLinesGroupData) => entity.groupType,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: ICuttingTaskLinesGroupData) => entity.groupName,
    },
    group_total     : {
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
        data          : (entity: ICuttingTaskLinesGroupData) => subgroupSumPics(entity.subgroups).toString() + ' шт. / ' + formatTimeWithLeadingZeros(subgroupSumTime(entity.subgroups)),
    },
    group_done      : {
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
        data          : (entity: ICuttingTaskLinesGroupData) => subgroupSumPics(entity.subgroups, true).toString() + ' шт. / ' + formatTimeWithLeadingZeros(subgroupSumTime(entity.subgroups, true)),
    },
    group_false     : {
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
        data          : (entity: ICuttingTaskLinesGroupData) => subgroupSumPics(entity.subgroups, false).toString() + ' шт. / ' + formatTimeWithLeadingZeros(subgroupSumTime(entity.subgroups, false)),
    },
    subgroup_title  : {
        header    : ['СМЕННОЕ ЗАДАНИЕ', ''],
        width     : DEFAULT_WIDTH,
        height    : DEFAULT_HEIGHT,
        show      : true,
        headerType: () => HEADER_TYPE,
        dataType  : () => DATA_TYPE,
        type      : () => DEFAULT_TYPE,
        // type          : (entity: ICuttingTaskLinesSubgroup) => entity.subgroupType,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: ICuttingTaskLinesSubgroup) => entity.subgroupName,
    },
    subgroup_total  : {
        header    : ['ВСЕГО', ''],
        width     : DEFAULT_WIDTH,
        height    : DEFAULT_HEIGHT,
        show      : true,
        headerType: () => HEADER_TYPE,
        dataType  : () => DATA_TYPE,
        type      : () => TOTAL_TYPE,
        // type          : (entity: ICuttingTaskLinesSubgroup) => entity.subgroupType,
        headerTextSize: HEADER_TEXT_SIZE,
        dataTextSize  : DATA_TEXT_SIZE,
        headerAlign   : HEADER_ALIGN,
        dataAlign     : DATA_ALIGN,
        data          : (entity: ICuttingTaskLinesSubgroup) => linesSumPics(entity.lines).toString() + ' шт. / ' + formatTimeWithLeadingZeros(linesSumTime(entity.lines)),
    },
    subgroup_done   : {
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
        data          : (entity: ICuttingTaskLinesSubgroup) => linesSumPics(entity.lines, true).toString() + ' шт. / ' + formatTimeWithLeadingZeros(linesSumTime(entity.lines, true)),
    },
    subgroup_false  : {
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
        data          : (entity: ICuttingTaskLinesSubgroup) => linesSumPics(entity.lines, false).toString() + ' шт. / ' + formatTimeWithLeadingZeros(linesSumTime(entity.lines, false)),
    },
})

// __ Сумма по подгруппе (Глухие, УШМ + окантователь, УШМ и т.д.)
const linesSumPics = (lines: ICuttingTaskLine[] = [], checkType: boolean | null = null) => lines.reduce((accLines, line) => {
    if (checkType === null) return accLines + line.amount
    if (checkType) {
        return line.finished_at ? accLines + line.amount : accLines
    } else {
        return !line.finished_at ? accLines + line.amount : accLines

    }
}, 0)

const linesSumTime = (lines: ICuttingTaskLine[] = [], checkType: boolean | null = null) => lines.reduce((accLines, line) => {
    if (checkType === null) return accLines + getCuttingTaskLineTime(line)
    if (checkType) {
        return line.finished_at ? accLines + getCuttingTaskLineTime(line) : accLines
    } else {
        return !line.finished_at ? accLines + getCuttingTaskLineTime(line) : accLines
    }
}, 0)


// __ Сумма по группе (АШМ, УШМ и Н/Д)
const subgroupSumPics = (subgroups: ICuttingTaskLinesSubgroup[] = [], checkType: boolean | null = null) => subgroups.reduce((accSubgroup, subgroup) => accSubgroup + linesSumPics(subgroup.lines, checkType), 0)
const subgroupSumTime = (subgroups: ICuttingTaskLinesSubgroup[] = [], checkType: boolean | null = null) => subgroups.reduce((accSubgroup, subgroup) => accSubgroup + linesSumTime(subgroup.lines, checkType), 0)

// __ Сумма по объекту (СЗ)
const groupSumPics = (groups: ICuttingTaskLinesGroupData[] = [], checkType: boolean | null = null) => groups.reduce((accGroup, group) => accGroup + subgroupSumPics(group.subgroups, checkType), 0)
const groupSumTime = (groups: ICuttingTaskLinesGroupData[] = [], checkType: boolean | null = null) => groups.reduce((accGroup, group) => accGroup + subgroupSumTime(group.subgroups, checkType), 0)

// __ Сумма по всем объектам (СЗ + объединенное)
// const dataSumPics     = (dataObjectsList: IDataObject[] = dataObjects.value!, checkType: boolean | null = null) => dataObjectsList.reduce((accObject, dataObject) => accObject + groupSumPics(dataObject.groups, checkType), 0)
// const dataSumTime     = (dataObjectsList: IDataObject[] = dataObjects.value!, checkType: boolean | null = null) => dataObjectsList.reduce((accObject, dataObject) => accObject + groupSumTime(dataObject.groups, checkType), 0)


// __ Формируем объект с данными для отображения
const prepareDataObjects = (): IDataObject[] => {
    const COLLAPSED_STATE = true


    let resultObject: IDataObject[] = []
    if (!props.cuttingDay) {
        return []
    }
    let unionTasks: ICuttingTaskLine[] = []
    props.cuttingDay.cutting_tasks.forEach((task, idx) => {
        unionTasks = [...unionTasks, ...task.cutting_lines]

        resultObject.push({
            taskTitle: `${task.position}. ${task.order.client.short_name} №${task.order.order_no_num}`,
            groups   : groupTaskLinesForExecute(task.cutting_lines),
            collapsed: COLLAPSED_STATE,
            id       : idx
        })
    })

    const resultObjectLength = resultObject.length

    resultObject.push({
        taskTitle: CUTTING_UNION_TASK_NAME,
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


// const totalTask = computed(() => {
//     if (!dataObjects.value) {
//         return 0
//     }
//     return dataObjects.value!.reduce((accObject, dataObject) => {
//         return accObject + dataObject.groups.reduce((accGroup, group) => {
//             return accGroup + group.subgroups.reduce((accSubgroup, subgroup) => {
//                 return accSubgroup + subgroup.lines.reduce((accLines, line) => accLines + line.amount, 0)
//             }, 0)
//         }, 0)
//     }, 0) || 0
// })
//
// console.log('totalTask: ', totalTask.value)


let totalCollapsedState = false
const toggleCollapsed   = () => {
    totalCollapsedState = !totalCollapsedState
    dataObjects.value!.forEach(dataObject => dataObject.collapsed = totalCollapsedState)
}

//
const toggleCollapsedTask = (dataObject: IDataObject) => {
    dataObject.collapsed = !dataObject.collapsed
}


const toggleCollapsedGroup = (group: ICuttingTaskLinesGroupData) => {
    group.collapsed = !group.collapsed
}


onMounted(() => {
    isLoading.value   = true
    dataObjects.value = prepareDataObjects()
    // console.log('data: ', dataObjects.value)
    isLoading.value = false
})

watch(() => props.cuttingDay, () => {
    dataObjects.value = prepareDataObjects()
}, { deep: true, immediate: true })

</script>

<style scoped>

</style>

