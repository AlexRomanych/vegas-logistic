<template>
    <div v-if="!isLoading" class="my-2">

        <!-- __ Удаление/добавление СЗ -->
        <div class="mb-4 uppercase cursor-pointer">
            <AppLabelTS
                :text="actionText"
                :type="actionType"
                width="w-[400px]"
                height="h-[50px]"
                rounded="4"
                align="center"
                @click="actionTask"
            />
        </div>

        <!-- __ Шапка СЗ -->
        <ExecuteTaskHeader
            :fields-width="sewingTaskFieldsWidth"
            :client-show="false"
            :order-info="false"
        />

        <!-- __ Сами СЗ -->
        <div v-for="sewingTask of sewingTasks" :key="sewingTask.id" class="bg-green-100">
            <ExecuteTask
                :fields-width="sewingTaskFieldsWidth"
                :sewing-task="sewingTask"
                :client-show="false"
                :order-info="false"
            />
        </div>
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

<script setup lang="ts">
import { onMounted, ref, watch, computed } from 'vue'

import type { IColorTypes, IRenderOrder, ISewingTask } from '@/types'

import { useSewingStore } from '@/stores/SewingStore.ts'

import { loaderHandler } from '@/app/helpers/helpers_render.ts'
import { useLoading } from 'vue-loading-overlay'

import ExecuteTaskHeader
    from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_execute/ExecuteTaskHeader.vue'
import ExecuteTask
    from '@/components/dashboard/manufacture/cells/sewing/sewing_components/sewing_execute/ExecuteTask.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppModalAsyncMultiline from '@/components/ui/modals/AppModalAsyncMultiline.vue'
import { checkCRUD } from '@/app/helpers/helpers_checks.ts'

interface IProps {
    order: IRenderOrder
    id: number
}

const props = defineProps<IProps>()

const sewingStore = useSewingStore()

const DEBUG     = true
const isLoading = ref(false)


// __ Объявляем переменные
const sewingTasks = ref<ISewingTask[]>([])

// __ Вычисляемые свойства
const actionText = computed(() => sewingTasks.value.length !== 0 ? 'Удалить сменное задание' : 'Создать сменное задание')
const actionType = computed(() => sewingTasks.value.length !== 0 ? 'danger' : 'success')


// __ Ширина полей для вывода СЗ
const COLLAPSED_WIDTH = 'w-[30px]'
const PROGRESS_WIDTH  = 'w-[266px]'

const sewingTaskFieldsWidth = {
    collapsed:     COLLAPSED_WIDTH,
    id:            'w-[30px]',
    position:      'w-[30px]',
    client:        'w-[190px]',
    order_no:      'w-[50px]',
    status:        'w-[140px]',
    progressTotal: PROGRESS_WIDTH,
    load_at:       'w-[163px]',
    comment:       'w-[793px]',
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
    const tasks: ISewingTask[] = await sewingStore.getSewingTasksByOrderId(props.id)
    sewingTasks.value          = tasks
        .map(task => ({ ...task, collapsed: true }))
}

// __ Удаляем/добавляем СЗ
const actionTask = async () => {

    let result
    if (sewingTasks.value.length !== 0) {

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

        result = await sewingStore.deleteSewingTasksByOrderId(props.id)

        sewingTasks.value = []

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

        result = await sewingStore.addSewingTasksByOrderId(props.id)
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
            DEBUG && console.log('sewingTasks: ', sewingTasks.value)
        },
        undefined,
        // false,
    )

    isLoading.value = false
})


</script>

<style scoped>

</style>
