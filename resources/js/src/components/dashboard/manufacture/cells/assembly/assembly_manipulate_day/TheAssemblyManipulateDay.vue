<template>

    <!-- __ Участки производства (Tabs/Sectors)-->
    <div class="flex m-2">
        <div v-for="tab of tabs" :key="tab.id">
            <div
                v-if="!isEmpty(tab)"
                :class="[activeTabIndex === tab.id ? 'bg-blue-300 border-2 border-blue-800 p-0.5' : 'p-0.5']"
                class="rounded-md transition-all"
            >
                <AppLabelMultiLineTS
                    :text="tab.sector.LABEL"
                    :type="tab.sector.TYPE"
                    align="center"
                    rounded="4"
                    text-size="mini"
                    width="w-[150px]"
                    @click="activeTabIndex = tab.id"
                />
            </div>
        </div>

        <!-- __ Комментарий к Дню СЗ -->
        <AppLabelTS
            v-if="renderDay.description"
            :text="renderDay.description"
            align="left"
            height="h-[50px]"
            rounded="4"
            text-size="mini"
            type="warning"
            width="w-[200px]"
        />

    </div>

    <!-- __ Сами Данные -->
    <div v-if="activeTabIndex">
        <AssemblyManipulateSector
            :active-task-id="activeTaskId"
            :assembly-day="day"
            :matrix="matrix[getTab().sector.NAME]"
            :sector="getTab().sector"
        />
    </div>

</template>

<script lang="ts" setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'

import type {
    IAssemblyDay,
    IAssemblyManipulateDay,
    IAssemblySector,
    IAssemblySectorKeys,
    IAssemblyTask,
    IAssemblyTaskChangeKeys,
    IAssemblyTaskLine,
    IMatrixManufactureTask,
    IPeriod,
} from '@/types'

import { useAssemblyStore } from '@/stores/AssemblyStore.ts'

import { ASSEMBLY_DAY_DRAFT, ASSEMBLY_SECTORS, ASSEMBLY_TASK_DRAFT, CHANGE_1, DAY_MANIPULATE_DRAFT, REDIRECT_KEY } from '@/app/constants/assembly.ts'

import {
    filterTaskBySectors,
    getSectorMaterialsMatrixTasks,
    // getAssemblyManipulationRenderTasks,
} from '@/app/helpers/manufacture/helpers_assembly.ts'
import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import AssemblyManipulateSector from '@/components/dashboard/manufacture/cells/assembly/assembly_manipulate_day/ManipulateDaySector.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'

interface ITab {
    id: number
    name: string
    sector: IAssemblySector
}

const assemblyStore = useAssemblyStore()

const route  = useRoute()
const router = useRouter()

const {
          globalAssemblyTasks,  // __ Все задания (Global State)
      } = storeToRefs(assemblyStore)

const isLoading = ref(false)
// let paramDate: string = ''

// __ Подготавливаем переменные
const renderDay      = ref<IAssemblyManipulateDay>(DAY_MANIPULATE_DRAFT)
const day            = ref<IAssemblyDay>(ASSEMBLY_DAY_DRAFT)
const tabs           = ref<ITab[]>([])
const activeTabIndex = ref<number | null>(null)
const activeTaskId   = ref<number | null>(null)
const paramDate      = ref('')
// const commonTask     = ref<IAssemblyTask>(JSON.parse(JSON.stringify(ASSEMBLY_TASK_DRAFT)))
// const matrix         = ref<Record<IAssemblySectorKeys, IMatrixManufactureGroup>>({} as Record<IAssemblySectorKeys, IMatrixManufactureGroup>)


// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---    Табы для группировки отображения           !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// __ Устанавливаем Активную вкладку. Тут потом будет доработка по установке активной вкладки при переходе
const setTabIndex = () => {
    const meta = localStorage.getItem(REDIRECT_KEY)
    if (meta) {
        const metaObj = JSON.parse(meta)
        if (metaObj?.sector_id) {

            // __ Активное СЗ
            activeTaskId.value = metaObj?.task_id

            const findTab = tabs.value.find(tab => tab.sector.ID === metaObj.sector_id)
            if (findTab) {
                activeTabIndex.value = findTab.id
                localStorage.removeItem(REDIRECT_KEY)
                return
            }
        }
    }

    const findTabIndex = tabs.value.findIndex(tab => tab.id !== 0)
    if (findTabIndex !== -1) {
        activeTabIndex.value = 1
    }
}

// __ Подготавливаем Табы Секторов
const prepareTabs = () => {
    Object.values(ASSEMBLY_SECTORS).forEach(value => {
        const tab: ITab = {
            id    : value.ID,
            name  : value.NAME,
            sector: value,
        }

        tabs.value.push(tab)
    })

    setTabIndex()
}

// __ Проверяем, пустой ли таб или нет (Есть ли в списке СЗ - СЗ с нужным Участком)
const isEmpty = (tab: ITab) => {
    let total = 0
    renderDay.value.tasks.forEach(task => {
        const findSector = task.stats.find(stat => stat.sector === tab.sector.NAME)
        if (findSector) {
            total += findSector.total_amount
        }
    })

    return total === 0
}

// __ Находим id таба по activeTabIndex
const getTab = () => {
    const tab = tabs.value.find(tab => tab.id === activeTabIndex.value)
    if (tab) {
        return tab
    }
    throw new Error('Tab not found')
}


// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---                   Логика                      !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!


// __ Загружаем СЗ на данную дату со всей инфой
const loadTasks = async () => {
    const period: IPeriod = { start: paramDate.value, end: paramDate.value }
    await assemblyStore.getAssemblyTasks(null, period)
}

// __ Загружаем Производственый День
const loadDay = async (change: IAssemblyTaskChangeKeys = CHANGE_1) => {
    const dayDate = paramDate.value.split(' ')[0]
    const days = await assemblyStore.getAssemblyDayByDateAndChange(dayDate, change) as IAssemblyDay[]
    if (days[0]) {
        day.value                = days[0]
        day.value.assembly_tasks = globalAssemblyTasks.value

        console.log('globalAssemblyTasks.value: ', globalAssemblyTasks.value)
    }
}

// __ Подготавливаем массив отображения
const getRenderDay = () => {
    renderDay.value = {
        action_at  : paramDate.value,
        tasks      : globalAssemblyTasks.value,
        description: null,
        comment    : null,
        collapsed  : true,
    }

    // const period: IPeriod = { start: date, end: date }
    // const renderDays      = getAssemblyManipulationRenderTasks(globalAssemblyTasks.value, period)
    // if (renderDays[0]) {
    //     renderDay.value = renderDays[0]
    // }
}


// __ Создаем объединенное СЗ
const commonTask = computed<IAssemblyTask>(() => {
    const comTask = JSON.parse(JSON.stringify(ASSEMBLY_TASK_DRAFT))

    const allTaskLines: IAssemblyTaskLine[] = []
    globalAssemblyTasks.value.forEach(task => {
        if (task.active) {
            task.assembly_lines.forEach(line => allTaskLines.push(line))
        }

    })

    comTask.action_at      = paramDate.value
    comTask.assembly_lines = allTaskLines

    console.log('calc')

    return comTask
})


// __ Добавляем Объединение СЗ
const addCommonTask = () => {
    renderDay.value.tasks.push(commonTask.value)
}

// __ Создаем объект отображения матрицы Группы --> Модели --> Материалы
const matrix = computed(() => {
    const resultMatrix: Record<IAssemblySectorKeys, IMatrixManufactureTask[]> = {} as Record<IAssemblySectorKeys, IMatrixManufactureTask[]>

    // __ Создаем карту активности СЗ
    const trueMap = new Map()
    globalAssemblyTasks.value.forEach(task => trueMap.set(task.id, task.active))

    Object.values(ASSEMBLY_SECTORS).forEach(value => {
        // __ Фильтруем по Участку
        const filtered           = filterTaskBySectors(renderDay.value.tasks, value.NAME)

        resultMatrix[value.NAME] = getSectorMaterialsMatrixTasks(filtered, trueMap)
    })

    console.log('calc matrix: ', resultMatrix)
    return resultMatrix
})

watch(() => globalAssemblyTasks.value, () => {
    // addCommonTask()
}, {deep: true})


onMounted(async () => {
    // warn: Порядок важен!
    isLoading.value = true

    await router.isReady().then(() => {
        paramDate.value = route.params.date as unknown as string
    })

    // __ Загружаем СЗ + Производственный день
    await Promise.all([
        loadTasks(),
        loadDay()
    ])

    getRenderDay()      // __ Оборачиваем в Day
    addCommonTask()     // __ Добавляем Общее СЗ
    prepareTabs()       // __ Подготавливаем Табы

    console.log('globalAssemblyTasks.value: ', globalAssemblyTasks.value)
    console.log('renderDay.value: ', renderDay.value)
    console.log('day.value: ', day.value)
    console.log('matrix: ', matrix.value)

    isLoading.value = false
})
</script>

<style scoped>

</style>
