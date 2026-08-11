<template>
    <div v-if="!isLoading" class="my-2">

        <div class="flex mb-4 uppercase cursor-pointer">
            <!-- __ Табы -->
            <div
                v-for="tab of tabs"
                :key="tab.position"
            >
                <!-- __ Таб: TODO: !!! Доделать крестики и галочки на выполненных задачах !!!   -->
                <AppLabelMultiLineTS
                    v-if="tab.show"
                    :text="tab.label"
                    :type="activeTabPosition === tab.position ? tab.typeActive : tab.type"
                    :width="BUTTON_WIDTH"
                    align="center"
                    class="start-group cursor-pointer"
                    rounded="4"
                    text-size="mini"
                    @click="activeTabPosition = tab.position"
                />
            </div>

            <!-- __ Удаление/добавление СЗ -->
            <AppLabelTS
                :text="actionText"
                :type="actionType"
                :width="BUTTON_WIDTH"
                align="center"
                height="h-[50px]"
                rounded="4"
                text-size="mini"
                @click="actionTask"
            />

        </div>

        <template v-if="activeTabPosition === 1">

            <div class="flex mb-2">
                <!-- __ Участки -->
                <div v-for="sector of sectors" :key="sector.ID">
                    <div
                        :class="[activeSector.ID === sector.ID ? 'bg-blue-300 border-2 border-blue-800 p-0.5' : 'p-0.5']"
                        class="rounded-md transition-all"
                    >
                        <AppLabelMultiLineTS
                            v-if="sector.SHOW"
                            :text="sector.LABEL"
                            :type="sector.TYPE"
                            :width="SECTOR_WIDTH"
                            align="center"
                            class="start-group cursor-pointer"
                            rounded="4"
                            text-size="mini"
                            @click="activeSector = sector"
                        />
                    </div>
                </div>
            </div>

            <!-- __ Шапка СЗ -->
            <ExecuteTaskHeader
                :client-show="false"
                :fields-width="assemblyTaskFieldsWidth"
                :order-info="false"
            />

            <!-- __ Сами СЗ -->
            <div v-for="assemblyTask of renderAssemblyTasks" :key="assemblyTask.id">
                <ExecuteTask
                    :assembly-task="assemblyTask"
                    :client-show="false"
                    :fields-width="assemblyTaskFieldsWidth"
                    :order-info="false"
                />
            </div>
        </template>
        <template v-else-if="activeTabPosition === 2">
            <OrderLines
                :order-lines="orderWithAssemblyTask ? orderWithAssemblyTask.lines : []"
                :show-assembly-sectors="true"
                :show-collapsed="false"
            />
        </template>

    </div>

    <!-- __ Модальное окно для сообщений -->
    <AppModalAsyncMultiline
        ref="appModalAsyncMultiline"
        :mode="modalInfoMode"
        :text="modalInfoText"
        :type="modalInfoType"
        ok-word="Понятно"
    />

</template>

<script lang="ts" setup>
import { onMounted, ref, computed } from 'vue'

import type { IColorTypes, IRenderOrder, IAssemblyTask, IRenderOrderAssemblyTask, } from '@/types'

import { useAssemblyStore } from '@/stores/AssemblyStore.ts'
import { useOrdersStore } from '@/stores/OrdersStore.ts'

import { loaderHandler } from '@/app/helpers/helpers_render.ts'
import { useLoading } from 'vue-loading-overlay'

import { ASSEMBLY_SECTORS, } from '@/app/constants/assembly.ts'

import { checkCRUD } from '@/app/helpers/helpers_checks.ts'

import AppModalAsyncMultiline from '@/components/ui/modals/AppModalAsyncMultiline.vue'
import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'

import ExecuteTaskHeader
    from '@/components/dashboard/manufacture/cells/assembly/assembly_execute/ExecuteTaskHeader.vue'
import ExecuteTask
    from '@/components/dashboard/manufacture/cells/assembly/assembly_execute/ExecuteTask.vue'
import OrderLines from '@/components/dashboard/orders/order_components/order_render/OrderLines.vue'


interface IProps {
    order: IRenderOrder
    id: number
}

interface ITab {
    show: boolean
    label: string[]
    position: number
    type: IColorTypes
    typeActive: IColorTypes
}

const props = defineProps<IProps>()

const assemblyStore = useAssemblyStore()
const ordersStore   = useOrdersStore()

const DEBUG     = true
const isLoading = ref(false)

// __ Объявляем константы
const BUTTON_WIDTH = 'w-[200px]'
const SECTOR_WIDTH = 'w-[150px]'

// __ Объявляем переменные
const assemblyTasks         = ref<IAssemblyTask[]>([])
const orderWithAssemblyTask = ref<IRenderOrderAssemblyTask | null>(null)

// __ Вычисляемые свойства
const actionText          = computed(() => assemblyTasks.value?.length !== 0 ? 'Удалить сменное задание' : 'Создать сменное задание')
const actionType          = computed(() => assemblyTasks.value?.length !== 0 ? 'danger' : 'success')
const renderAssemblyTasks = computed(() => {
    const tasks = assemblyTasks.value
        .map(task => {
            const assemblyLines = task.assembly_lines
                .map(line => {
                    const sectorLines = line.sector_lines
                        .filter(sector => sector.sector === activeSector.value.NAME)
                        .toSorted((a, b) => a.material_name.localeCompare(b.material_name))
                    return sectorLines.length > 0 ? { ...line, sector_lines: sectorLines } : undefined
                })
                .filter(line => line !== undefined)
            return { ...task, assembly_lines: assemblyLines, collapsed: false }
        })
    .filter(task => task.assembly_lines.length > 0)

    // console.log('tasks: ', tasks)
    return tasks
})

// __ Табы
const tabs              = ref<ITab[]>([])
const activeTabPosition = ref(1)

// __ Участки
const sectors      = computed(() => Object.entries(ASSEMBLY_SECTORS).map(([, value]) => value))
const activeSector = ref(sectors.value[0])

const setTabs = () => {
    tabs.value = []
    tabs.value.push({
        show      : true,
        label     : ['Сменное', 'задание'],
        position  : 1,
        type      : 'stone',
        typeActive: 'primary',
    })
    tabs.value.push({
        show      : true,
        label     : ['Содержимое', 'сменного задания'],
        position  : 2,
        type      : 'dark',
        typeActive: 'primary',
    })
}

// __ Ширина полей для вывода СЗ
const COLLAPSED_WIDTH = 'w-[30px]'
const PROGRESS_WIDTH  = 'w-[264px]'

const assemblyTaskFieldsWidth = {
    collapsed    : COLLAPSED_WIDTH,
    id           : 'w-[30px]',
    position     : 'w-[30px]',
    client       : 'w-[190px]',
    order_no     : 'w-[50px]',
    status       : 'w-[140px]',
    progressTotal: PROGRESS_WIDTH,
    load_at      : 'w-[228px]',
    comment      : 'w-[1059px]',
}

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !!! ---                Ошибки                         !!!
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// __ Тип для модального окна Сообщений
const modalInfoType          = ref<IColorTypes>('danger')
const modalInfoText          = ref<string | string[]>('')
const modalInfoMode          = ref<'inform' | 'confirm'>('confirm')
const appModalAsyncMultiline = ref<InstanceType<typeof AppModalAsyncMultiline> | null>(null)        // Получаем ссылку на модальное окно с асинхронной функцией

// __ Показываем сообщение об ошибке
async function showError(error: string | string[] | null = null) {
    modalInfoType.value = 'danger'
    modalInfoMode.value = 'inform'

    let renderError = ['Упс! Что-то пошло не так!', 'Ошибка при обработке запроса!']
    if (typeof error === 'string' && error.length > 0) {
        renderError = [error]
    } else if (Array.isArray(error) && error.length > 0) {
        renderError = error
    }

    modalInfoText.value = renderError
    await appModalAsyncMultiline.value!.show()
}


// __ Получаем СЗ с сервера
const getTasks = async () => {
    const [tasks, orderWithTask]: [IAssemblyTask[], IRenderOrderAssemblyTask] = await Promise.all([
        assemblyStore.getAssemblyTasksByOrderId(props.id),
        ordersStore.getOrdersWithAssemblyTaskLines(props.id)
    ])

    assemblyTasks.value = tasks.map(task => ({ ...task, collapsed: true }))

    const ORDER_OF_SECTORS = [
        ASSEMBLY_SECTORS.ASSEMBLY_TASK_SECTOR_COCONUT.NAME,
        ASSEMBLY_SECTORS.ASSEMBLY_TASK_SECTOR_LATEX.NAME,
        ASSEMBLY_SECTORS.ASSEMBLY_TASK_SECTOR_LAYER.NAME,
        ASSEMBLY_SECTORS.ASSEMBLY_TASK_SECTOR_FOAM_LAYER.NAME,
        ASSEMBLY_SECTORS.ASSEMBLY_TASK_SECTOR_FOAM_SIDE.NAME,
    ]

    if (orderWithTask) {
        orderWithAssemblyTask.value = {
            ...orderWithTask,
            lines: orderWithTask.lines.map(line => {
                return {
                    ...line,
                    collapsed_assembly_sectors: true,
                    assembly_lines            : line.assembly_lines?.map(assemblyLine => {
                        return {
                            ...assemblyLine,
                            sectors: assemblyLine.sectors?.toSorted((a, b) => {
                                // 2. Находим индексы текущих элементов в нашем эталоне
                                const indexA = ORDER_OF_SECTORS.indexOf(a.sector as typeof ORDER_OF_SECTORS[number])
                                const indexB = ORDER_OF_SECTORS.indexOf(b.sector as typeof ORDER_OF_SECTORS[number])

                                // 3. Сравниваем индексы (числа) обычным вычитанием
                                return indexA - indexB
                            })
                        }
                    })
                }
            }),
        }
    }
}

// __ Удаляем/добавляем СЗ
const actionTask = async () => {
    //
    let result
    if (assemblyTasks.value?.length !== 0) {

        // __ Удаляем СЗ
        modalInfoType.value = 'danger'
        modalInfoMode.value = 'confirm'
        modalInfoText.value = [
            'Сменное задание будет удалено.',
            'Продолжить?',
        ]

        const answer = await appModalAsyncMultiline.value!.show()
        if (!answer) {
            return
        }

        result = await assemblyStore.deleteAssemblyTasksByOrderId(props.id)

        assemblyTasks.value = []

    } else {

        // __ Добавляем СЗ
        modalInfoType.value = 'primary'
        modalInfoMode.value = 'confirm'
        modalInfoText.value = [
            'Сменное задание будет добавлено.',
            'Продолжить?',
        ]

        const answer = await appModalAsyncMultiline.value!.show()
        if (!answer) {
            return
        }

        result = await assemblyStore.addAssemblyTasksByOrderId(props.id)
        await getTasks()
    }

    if (checkCRUD(result)) {
        modalInfoType.value = 'success'
        modalInfoMode.value = 'inform'
        modalInfoText.value = [result.payload]
        await appModalAsyncMultiline.value!.show()
    } else {
        await showError()
    }
}


onMounted(async () => {
    isLoading.value = true

    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {

            await getTasks()
            if (DEBUG) console.log('assemblyTask: ', assemblyTasks.value)

            setTabs()
        },
        undefined,
        // false,
    )

    isLoading.value = false
})


</script>

<style scoped>

</style>
