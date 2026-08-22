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
    </div>

    <!-- __ Сами Данные -->
    <div v-if="activeTabIndex">
        <AssemblyManipulateSector
            :matrix="matrix[getTab().sector.NAME]"
            :sector="getTab().sector"
        />
    </div>

</template>

<script lang="ts" setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { storeToRefs } from 'pinia'

import type {
    IAssemblyManipulateDay,
    IAssemblySector,
    IAssemblySectorKeys,
    IAssemblyTask,
    IAssemblyTaskLine,
    IMatrixManufactureTask,
    IPeriod,
} from '@/types'

import { useAssemblyStore } from '@/stores/AssemblyStore.ts'

import { ASSEMBLY_SECTORS, ASSEMBLY_TASK_DRAFT, DAY_MANIPULATE_DRAFT } from '@/app/constants/assembly.ts'

import { filterTaskBySectors, getAssemblyManipulationRenderTasks, getSectorMaterialsMatrixTasks } from '@/app/helpers/manufacture/helpers_assembly.ts'
import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import AssemblyManipulateSector from '@/components/dashboard/manufacture/cells/assembly/assembly_manipulate_day/ManipulateDaySector.vue'

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

const isLoading       = ref(false)
let paramDate: string = ''

// __ Подготавливаем переменные
const renderDay      = ref<IAssemblyManipulateDay>(DAY_MANIPULATE_DRAFT)
const tabs           = ref<ITab[]>([])
const activeTabIndex = ref<number | null>(null)
const commonTask     = ref<IAssemblyTask>(JSON.parse(JSON.stringify(ASSEMBLY_TASK_DRAFT)))
// const matrix         = ref<Record<IAssemblySectorKeys, IMatrixManufactureGroup>>({} as Record<IAssemblySectorKeys, IMatrixManufactureGroup>)


// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---    Табы для группировки отображения           !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// __ Устанавливаем Активную вкладку. Тут потом будет доработка по установке активной вкладки при переходе
const setTabIndex = () => {
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
const loadTasks = async (date: string) => {
    const period: IPeriod = { start: date, end: date }
    await assemblyStore.getAssemblyTasks(null, period)
}

// __ Подготавливаем массив отображения
const getRenderDay = (date: string) => {
    const period: IPeriod = { start: date, end: date }
    const renderDays      = getAssemblyManipulationRenderTasks(globalAssemblyTasks.value, period)
    if (renderDays[0]) {
        renderDay.value = renderDays[0]
    }
}

// __ Добавляем Объединение СЗ
const addCommonTask = (date: string) => {
    // const commonTask = structuredClone(ASSEMBLY_TASK_DRAFT) as DeepWritable<typeof ASSEMBLY_TASK_DRAFT>

    const allTaskLines: IAssemblyTaskLine[] = []
    globalAssemblyTasks.value.forEach(task => {
        task.assembly_lines.forEach(line => allTaskLines.push(line))
    })

    commonTask.value.action_at      = date
    commonTask.value.assembly_lines = allTaskLines

    renderDay.value.tasks.push(commonTask.value)
}


// __ Создаем объект отображения матрицы Группы --> Модели --> Материалы
const matrix = computed(() => {
    const resultMatrix: Record<IAssemblySectorKeys, IMatrixManufactureTask[]> = {} as Record<IAssemblySectorKeys, IMatrixManufactureTask[]>

    Object.values(ASSEMBLY_SECTORS).forEach(value => {
        const renderTasks        = JSON.parse(JSON.stringify(renderDay.value.tasks))
        const filtered           = filterTaskBySectors(renderTasks, value.NAME)
        resultMatrix[value.NAME] = getSectorMaterialsMatrixTasks(filtered)

    })
    return resultMatrix
})


onMounted(async () => {
    // warn: Порядок важен!
    isLoading.value = true


    await router.isReady().then(() => {
        paramDate = route.params.date as unknown as string
    })


    await loadTasks(paramDate)      // __ Загружаем СЗ
    getRenderDay(paramDate)         // __ Оборачиваем в Day
    addCommonTask(paramDate)        // __ Добавляем Общее СЗ
    prepareTabs()                   // __ Подготавливаем Табы

    console.log('renderDay.value: ', renderDay.value)
    console.log('matrix: ', matrix.value)

    isLoading.value = false
    // console.log('editMode.value: ', editMode.value)
})
</script>

<style scoped>

</style>
