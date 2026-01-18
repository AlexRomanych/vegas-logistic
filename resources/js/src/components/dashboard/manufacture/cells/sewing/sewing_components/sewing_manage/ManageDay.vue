<template>

    <div
        :class="[shadowColor, globalSewingTaskFullDaysShow ? 'min-w-[379px]' : 'min-w-[184px]']"
        class="m-1 pb-0.5 border-[1px] rounded border-slate-600 bg-slate-200 w-fit min-w-[184px] min-h-[129px] shadow-xl"
    >

        <!-- __ День недели -->
        <div class="mr-1">
            <AppLabelTS
                :height="DEFAULT_HEIGHT"
                :text="renderDate"
                :type="dateType"
                align="center"
                rounded="rounded-[4px]"
                text-size="small"
                width="w-full"
            />
        </div>

        <!-- __ Шапка -->
        <div v-if="getTotalAmountDay || getTotalTimeDay" class="flex">

            <!-- __ Клиент -->
            <AppLabelTS
                :height="DEFAULT_HEIGHT"
                :text-size="DATA_HEADER_TEXT_SIZE"
                :type="TOTALS_TYPE"
                :width="columnsWidth.client"
                align="center"
                rounded="rounded-[4px]"
                text="Клиент:"
            />

            <!-- __ Номер заказа -->
            <AppLabelTS
                :height="DEFAULT_HEIGHT"
                :text-size="DATA_HEADER_TEXT_SIZE"
                :type="TOTALS_TYPE"
                :width="columnsWidth.orderNo"
                align="center"
                rounded="rounded-[4px]"
                text="№"
            />

            <!-- __ Сумма -->
            <AppLabelTS
                :height="DEFAULT_HEIGHT"
                :text-size="DATA_HEADER_TEXT_SIZE"
                :type="TOTALS_TYPE"
                :width="columnsWidth.amount"
                align="center"
                rounded="rounded-[4px]"
                text="Σ"
            />

            <!-- __ УШМ -->
            <AppLabelTS
                v-if="globalSewingTaskFullDaysShow"
                :height="DEFAULT_HEIGHT"
                :text-size="DATA_HEADER_TEXT_SIZE"
                :type="TOTALS_TYPE"
                :width="columnsWidth.machine"
                align="center"
                rounded="rounded-[4px]"
                text="У"
            />

            <!-- __ АШМ -->
            <AppLabelTS
                v-if="globalSewingTaskFullDaysShow"
                :height="DEFAULT_HEIGHT"
                :text-size="DATA_HEADER_TEXT_SIZE"
                :type="TOTALS_TYPE"
                :width="columnsWidth.machine"
                align="center"
                rounded="rounded-[4px]"
                text="А"
            />

            <!-- __ Глухие сложные -->
            <AppLabelTS
                v-if="globalSewingTaskFullDaysShow"
                :height="DEFAULT_HEIGHT"
                :text-size="DATA_HEADER_TEXT_SIZE"
                :type="TOTALS_TYPE"
                :width="columnsWidth.machine"
                align="center"
                rounded="rounded-[4px]"
                text="ГС"
            />

            <!-- __ Глухие простые -->
            <AppLabelTS
                v-if="globalSewingTaskFullDaysShow"
                :height="DEFAULT_HEIGHT"
                :text-size="DATA_HEADER_TEXT_SIZE"
                :type="TOTALS_TYPE"
                :width="columnsWidth.machine"
                align="center"
                rounded="rounded-[4px]"
                text="ГП"
            />

            <!-- __ Глухие Неопознанные -->
            <AppLabelTS
                v-if="globalSewingTaskFullDaysShow"
                :height="DEFAULT_HEIGHT"
                :text-size="DATA_HEADER_TEXT_SIZE"
                :type="TOTALS_TYPE"
                :width="columnsWidth.machine"
                align="center"
                color="red"
                rounded="rounded-[4px]"
                text="Н"
            />
        </div>

        <!-- __ Загрузки -->
        <!--<div v-for="(load, idx) of day" :key="idx">-->
        <!--    <PlanItem-->
        <!--        :columns-width="columnsWidth"-->
        <!--        :load="load"-->
        <!--    />-->
        <!--</div>-->


        <!--<div :class="fabricsStore.globalOrderManageChangeFlag ? 'opacity-50' : ''">-->
        <!-- __ Сами СЗ с возможностью перетаскивания -->
        <draggable
            :="dragOptions"
            :disabled="!isDragging"
            :list="day"
            :move="checkMove"
            class="min-h-[25px]"
            item-key="id"
            tag="div"
            @end="finishDrag"
            @start="startDrag"
        >
            <template #item="{ element, index }">

                <ManageItem
                    :amount-and-time="getAmountAndTime(element)"
                    :columns-width="columnsWidth"
                    :index="index"
                    :item="element"
                    @click="showSewingTaskCard(element)"
                />

            </template>

            <!--<template #header>-->
            <!--    <div-->
            <!--        v-if="!day || day.length === 0"-->
            <!--        class="text-center p-4 text-slate-400 border-dashed border-2 border-slate-300 rounded m-1 cursor-pointer hover:bg-slate-50 transition-colors"-->
            <!--    >-->
            <!--        Перетащите сюда-->
            <!--    </div>-->
            <!--</template>-->

            <!--<template #footer>-->
            <!--    <div-->
            <!--        v-if="day && day.length > 0"-->
            <!--        class="text-center p-2 text-slate-400 border-dashed border-2 border-slate-300 rounded m-1 cursor-pointer hover:bg-slate-50 transition-colors"-->
            <!--    >-->
            <!--        Добавить ещё-->
            <!--    </div>-->
            <!--</template>-->
        </draggable>


        <!--</div>-->


        <!-- __ Разделительная линия -->
        <div v-if="getTotalAmountDay || getTotalTimeDay" class="flex">
            <TheDividerLine/>
        </div>

        <!-- __ Итого -->
        <div v-if="getTotalAmountDay || getTotalTimeDay" class="flex">
            <AppLabelTS
                :height="heightTotals"
                :type="TOTALS_TYPE"
                :width="columnsWidth.common"
                align="center"
                rounded="rounded-[4px]"
                text="Всего:"
                text-size="mini"
            />

            <!-- __ Количество + Трудозатраты Общие -->
            <ManageItemDataLabel
                :amount="getTotalAmountDay"
                :height="heightTotals"
                :reference="REFERENCE_TIME * 4"
                :time="getTotalTimeDay"
                :time-show="globalSewingTaskTimesShow"
                :type="TOTALS_TYPE"
                :width="columnsWidth.amount"

                class="plan-item"
            />

            <!-- __ Количество + Трудозатраты УШМ -->
            <ManageItemDataLabel
                v-if="globalSewingTaskFullDaysShow"
                :amount="amountAndTimeTotalDay[SEWING_MACHINES.UNIVERSAL].amount"
                :height="heightTotals"
                :reference="REFERENCE_TIME"
                :time="amountAndTimeTotalDay[SEWING_MACHINES.UNIVERSAL].time"
                :time-show="globalSewingTaskTimesShow"
                :type="TOTALS_TYPE"
                :width="columnsWidth.amount"
                class="plan-item"
            />

            <!-- __ Количество + Трудозатраты АШМ -->
            <ManageItemDataLabel
                v-if="globalSewingTaskFullDaysShow"
                :amount="amountAndTimeTotalDay[SEWING_MACHINES.AUTO].amount"
                :height="heightTotals"
                :reference="REFERENCE_TIME"
                :time="amountAndTimeTotalDay[SEWING_MACHINES.AUTO].time"
                :time-show="globalSewingTaskTimesShow"
                :type="TOTALS_TYPE"
                :width="columnsWidth.amount"
                class="plan-item"
            />

            <!-- __ Количество + Трудозатраты Solid Hard -->
            <ManageItemDataLabel
                v-if="globalSewingTaskFullDaysShow"
                :amount="amountAndTimeTotalDay[SEWING_MACHINES.SOLID_HARD].amount"
                :height="heightTotals"
                :reference="REFERENCE_TIME"
                :time="amountAndTimeTotalDay[SEWING_MACHINES.SOLID_HARD].time"
                :time-show="globalSewingTaskTimesShow"
                :type="TOTALS_TYPE"
                :width="columnsWidth.amount"
                class="plan-item"
            />

            <!-- __ Количество + Трудозатраты Solid Lite -->
            <ManageItemDataLabel
                v-if="globalSewingTaskFullDaysShow"
                :amount="amountAndTimeTotalDay[SEWING_MACHINES.SOLID_LITE].amount"
                :height="heightTotals"
                :reference="REFERENCE_TIME"
                :time="amountAndTimeTotalDay[SEWING_MACHINES.SOLID_LITE].time"
                :time-show="globalSewingTaskTimesShow"
                :type="TOTALS_TYPE"
                :width="columnsWidth.amount"
                class="plan-item"
            />

            <!-- __ Количество + Трудозатраты Неопознанные -->
            <ManageItemDataLabel
                v-if="globalSewingTaskFullDaysShow"
                :amount="amountAndTimeTotalDay[SEWING_MACHINES.UNDEFINED].amount"
                :color="amountAndTimeTotalDay[SEWING_MACHINES.UNDEFINED].amount === 0 ? '' : 'red'"
                :height="heightTotals"
                :reference="null"
                :time="amountAndTimeTotalDay[SEWING_MACHINES.UNDEFINED].time"
                :time-show="globalSewingTaskTimesShow"
                :type="TOTALS_TYPE"
                :width="columnsWidth.amount"
                class="plan-item"
            />

        </div>


    </div>

    <!-- __ Карточка СЗ -->
    <ManageTaskCard
        ref="manageTaskCard"
        :task="taskCard"
        :mode="modalMode"
        :text="modalText"
        :type="modalType"
    />

</template>

<script lang="ts" setup>
import type {
    IColorTypes,
    IAmountAndTime,
    IPlanMatrixDayItem,
    ISewingMachineKeys,
    ISewingTask,
    // IPlanMatrixDay,
    // ISewingTaskLine
} from '@/types'

import { computed, ref, } from 'vue'
import { storeToRefs } from 'pinia'
import draggable from 'vuedraggable'

import { usePlansStore } from '@/stores/PlansStore.ts'
import { useSewingStore } from '@/stores/SewingStore.ts'

import { formatDateInFullFormat, isHoliday, isToday } from '@/app/helpers/helpers_date'
import { ifDateInPeriod } from '@/app/helpers/plan/helpers_plan.ts'

import { SEWING_MACHINES, SEWING_TASK_DRAFT } from '@/app/constants/sewing.ts'

import ManageItem from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_manage/ManageItem.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import TheDividerLine from '@/components/ui/dividers/TheDividerLine.vue'
import ManageItemDataLabel
    from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_manage/ManageItemDataLabel.vue'
import ManageTaskCard
    from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_manage/ManageTaskCard.vue'


type IDay = ISewingTask & IPlanMatrixDayItem

interface IProps {
    date: Date,
    day?: IDay[]
}


const props = withDefaults(defineProps<IProps>(), {
    day: () => [],
})

const emits = defineEmits<{
    (e: 'drag-and-drop'): void,
}>()


// __ Данные из Хранилища
const sewingStore = useSewingStore()

const { globalSewingTaskTimesShow, globalSewingTaskFullDaysShow } = storeToRefs(sewingStore)

const planStore            = usePlansStore()
const { planPeriodGlobal } = storeToRefs(planStore)


const DEFAULT_HEIGHT = 'h-[25px]'

const TOTALS_TYPE: IColorTypes = 'stone'
const DATA_HEADER_TEXT_SIZE    = 'mini'
const REFERENCE_TIME           = 10.5 * 60 * 60 // в секунды


// __ Высота под Итого
const heightTotals = computed(() => globalSewingTaskTimesShow.value ? 'h-[80px]' : 'h-[40px]')

// __ Дата
const renderDate = computed(() => formatDateInFullFormat(props.date, !globalSewingTaskFullDaysShow.value))
// const renderDate = computed(() => getDateFromDateTimeString(props.date))

// TODO: Переписать все в dateType()
const isHolidayDay = computed(() => isHoliday(props.date))
const isTodayDay   = computed(() => isToday(props.date))
const isDayInRange = computed(() => ifDateInPeriod(props.date, planPeriodGlobal.value))
// const isActiveDay = computed(() => false)
const dateType     = computed((): IColorTypes => {
    if (isTodayDay.value) return 'success'
    if (!isDayInRange.value) return 'dark'
    if (isHolidayDay.value) return 'danger'
    return 'primary'
})
// ----------------------------------------


// __ Ширина колонок
const columnsWidth = {
    client:  'w-[90px]',
    orderNo: 'w-[45px]',
    amount:  'w-[35px]',
    common:  'w-[139px]',
    machine: 'w-[35px]',
}

// __ Цвет тени
const shadowColor = computed(() => {
    switch (dateType.value) {
        case 'success':
            return 'shadow-green-300'
        case 'danger':
            return 'shadow-red-300'
        case 'primary':
            return 'shadow-blue-300'
        case 'dark':
            return 'shadow-slate-400'
        default:
            return 'shadow-slate-400'
    }
})


// __ Опции для draggable
const dragOptions = computed(() => {
    return {
        animation:   300,
        group:       'orders',
        ghostClass:  'ghost',
        dragClass:   'drag',
        chosenClass: 'chosen',
        // sort: true,
        // disabled: false, // Выносим в отдельное свойство
    }
})
const isDragging  = ref(true)

const checkMove  = (evt: any) => {
    return true
}
const startDrag  = (evt: any) => {
    // const element = evt.item._underlying_vm_
    // console.log('startDrag: ', evt.oldIndex)
    // console.log('element: ', element)
}
const finishDrag = (evt: any) => {
    // const element = evt.item._underlying_vm_
    emits('drag-and-drop')
}


// __ Создаем сам объект данных с ключами из SEWING_MACHINES и {time: 0, amount: 0} и инициализируем его нулями
const createAmountAndTimeObj = () => Object.values(SEWING_MACHINES).reduce((acc, value) => {
    acc[value] = { time: 0, amount: 0 }
    return acc
}, {} as IAmountAndTime)

// __ Создаем объект для вывода данных в шаблоне
// __ Ключи - это типы машин, а значения - это объекты с ключами time и amount
const getAmountAndTime = (item: ISewingTask) => {

    // __ Функция для получения типа машины по ключу
    const getMachineType = (machineType: ISewingMachineKeys) => {
        const findMachineTypeKey = Object.keys(SEWING_MACHINES).find(key => SEWING_MACHINES[key] === machineType)
        return findMachineTypeKey ? SEWING_MACHINES[findMachineTypeKey] : null
    }

    // __ Создаем сам объект данных и инициализируем его нулями
    const amountAndTimeObj = createAmountAndTimeObj()

    // __ Проходим по всем sewing_lines и суммируем данные
    item.sewing_lines?.forEach(sewing_line => {
        // __ Получаем тип машины модели
        let machineType: ISewingMachineKeys | null = SEWING_MACHINES.UNDEFINED
        if (sewing_line.order_line.model.base && !sewing_line.order_line.model.cover) {         // __ Это условие того, что элемент - чехол
            machineType = getMachineType(sewing_line.order_line.model.base.machine_type)        // __ Получаем тип машины модели по базовой модели
        } else if (!sewing_line.order_line.model.base && sewing_line.order_line.model.cover) {  // __ Это условие того, что элемент - матрас
            machineType = getMachineType(sewing_line.order_line.model.main.machine_type)        // __ Получаем тип машины модели по основной модели
        } else if (!sewing_line.order_line.model.base && !sewing_line.order_line.model.cover) { // __ Это условие того, что элемент - расчетный)
            machineType = getMachineType(sewing_line.order_line.model.main.machine_type)        // __ Получаем тип машины модели по основной модели
        }
        if (!machineType) {
            return  // __ Страховочка
        }

        // __ Получаем количество
        if (machineType === SEWING_MACHINES.AVERAGE) {
            if (sewing_line.amount_avg) {
                Object.entries(sewing_line.amount_avg).forEach(([key, value]) => {
                    const machineType = getMachineType(key as ISewingMachineKeys)
                    if (!machineType) {
                        return  // __ Страховочка
                    }
                    amountAndTimeObj[machineType].amount += value
                })
            } else {
                console.log('Error! Amount_avg is not defined for: ' + sewing_line.order_line.model.main.name)
            }
        } else {
            amountAndTimeObj[machineType].amount += sewing_line.amount
        }

        // __ Получаем время
        Object.entries(sewing_line.time).forEach(([key, value]) => {
            const machineTypeKey = key.replace('time_', '') as ISewingMachineKeys
            const machineType    = getMachineType(machineTypeKey)
            if (!machineType) {
                return  // __ Страховочка
            }
            amountAndTimeObj[machineType].time += value
        })
    })
    return amountAndTimeObj
}

// __ Общее количество и время в виде Объекта
const amountAndTimeTotalDay = computed(() => {

    // __ Создаем сам объект данных и инициализируем его нулями
    const amountAndTimeObj = createAmountAndTimeObj()

    props.day.forEach(sewingTask => {
        const amountAndTime = getAmountAndTime(sewingTask)

        Object.entries(amountAndTime).forEach(([key, value]) => {
            amountAndTimeObj[key].amount += value.amount
            amountAndTimeObj[key].time += value.time
        })
    })

    return amountAndTimeObj
})

// __ Общее Количество за день
const getTotalAmountDay = computed(() => Object.values(amountAndTimeTotalDay.value).reduce((acc, item) => item.amount + acc, 0))

// __ Общие Трудозатраты за день
const getTotalTimeDay = computed(() => Object.values(amountAndTimeTotalDay.value).reduce((acc, item) => item.time + acc, 0))




// __ Тип для модального окна
const modalType      = ref<IColorTypes>('primary')
const modalText      = ref<string>('')
const modalMode      = ref<'inform' | 'confirm'>('inform')
const manageTaskCard = ref<InstanceType<typeof ManageTaskCard> | null>(null)         // Получаем ссылку на модальное окно с асинхронной функцией

const taskCard = ref<ISewingTask>(SEWING_TASK_DRAFT)

const showSewingTaskCard = async (sewingTask: ISewingTask) => {
    modalText.value = `Расширение Файла не соответствует шаблону !!!`
    taskCard.value = sewingTask
    await manageTaskCard.value!.show(sewingTask)             // показываем модалку и ждем ответ
}


// watchEffect(() => {
//
// })


</script>

<style scoped>
/* Стили для draggable */
.ghost {
    opacity: 0.5;
    background: #c8ebfb;
}

.chosen {
    background: #e1f5fe;
}

.drag {
    opacity: 0.8;
    cursor: grabbing;
}

/* Убедимся, что draggable контейнер занимает пространство даже когда пустой */
:deep(.sortable-chosen) {
    background-color: rgba(226, 232, 240, 0.5);
}

:deep(.sortable-ghost) {
    opacity: 0.5;
}

.plan-item {
    @apply cursor-pointer;
}
</style>

