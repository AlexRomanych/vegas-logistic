<template>
    <div v-if="!isLoading" class="mt-2 ml-2">
        <div class="flex justify-start">
            <AppLabelMultiLineTS
                v-for="tab in tabs"
                :key="tab.name"
                :text="tab.displayName"
                :type="activeTabName === tab.name ? 'indigo' : 'dark'"
                :width="tab.width"
                align="center"
                @click="setActiveTab(tab)"
                rounded="4"
                class="shadow cursor-pointer"
            />
        </div>

        <!-- __ Разделительная линия -->
        <TheDividerLineTS />

        <div>
            <Suspense v-if="activeComponent">
                <component :is="activeComponent"
                           :order="order"
                           :id="paramId"
                           v-on="dynamicEvents"

                />
                <template #fallback>
                    <div>Загрузка компонента {{ activeTabName }}...</div>
                </template>
            </Suspense>
            <div v-else>Выберите вкладку для отображения содержимого.</div>
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
import type { IColorTypes, IRenderOrder, IRenderOrderLine } from '@/types'

import { onMounted, ref, shallowRef, computed, defineAsyncComponent /* provide, watch */ } from 'vue'
import { useRoute, useRouter } from 'vue-router'

import { SEWING_TASK_STATUSES } from '@/app/constants/sewing.ts'
import { CUTTING_TASK_STATUSES } from '@/app/constants/cutting.ts'

import { useOrdersStore } from '@/stores/OrdersStore'
// import { useSewingStore } from '@/stores/SewingStore'

import { formatDateInFullFormat, formatDateTime } from '@/app/helpers/helpers_date'
import { checkCRUD } from '@/app/helpers/helpers_checks.ts'
// import { loadAsyncComponent } from '@/app/composable/loadAsyncComponent.ts'
// import { OrderKey, IdKey } from './order_components/order_card/injectionKeys'

// __ Loader
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'

import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import TheDividerLineTS from '@/components/ui/dividers/TheDividerLineTS.vue'
import AppModalAsyncMultiline from '@/components/ui/modals/AppModalAsyncMultiline.vue'


interface ITab {
    name: string
    displayName: string | string[]
    component: string // Строковое имя компонента
    icon: string
    active: boolean // Это поле можно удалить, activeTabName будет управлять активностью
    width: string
    color: string
}


const route  = useRoute()
const router = useRouter()                 // Определяем роутер

const ordersStore = useOrdersStore()
// const sewingStore = useSewingStore()


// __ Тут путь к папке с динамическими компонентами
// const PATH_TO_COMPONENTS = '/resources/js/src/components/dashboard/orders/order_components/order_card/tabs'
const COMPONENTS = import.meta.glob('/resources/js/src/components/dashboard/orders/order_components/order_card/tabs/*.vue')
// const PATH_TO_COMPONENTS = 'components/dashboard/orders/order_components/order_card/tabs'

// __ Подготавливаем переменные
const order     = ref<IRenderOrder | null>(null)
const isLoading = ref(false)
const paramId   = ref<number>(-1)

// __ Определяем массив вкладок
const TAB_NAME_COMMON  = 'Общие'
const TAB_NAME_CONTEXT = 'Содержимое'
const TAB_NAME_SEWING  = 'Пошив'


const TAB_WIDTH             = 'w-[150px]'
const DEFAULT_ACTIVE_COLOR  = '#1A2F6F'
// const DEFAULT_PASSIVE_COLOR = '#111C3A'

const tabs: ITab[] = [
    {
        name:        'Общие',
        displayName: ['Общая', 'информация'],
        component:   'Common',
        icon:        '✨',
        active:      false,
        width:       TAB_WIDTH,
        color:       DEFAULT_ACTIVE_COLOR,
    },
    {
        name:        TAB_NAME_CONTEXT,
        displayName: ['Содержимое', 'Заявки'],
        component:   'Context',
        icon:        '✨',
        active:      false,
        width:       TAB_WIDTH,
        color:       DEFAULT_ACTIVE_COLOR,
    },
    {
        name:        'Закрой',
        displayName: ['СЗ:', 'Раскрой'],
        component:   'Cutting',
        icon:        '🚀',
        active:      false,
        width:       TAB_WIDTH,
        color:       DEFAULT_ACTIVE_COLOR,
    },
    {
        name:        TAB_NAME_SEWING,
        displayName: ['СЗ:', 'Пошив'],
        component:   'Sewing',
        icon:        '🚀',
        active:      false,
        width:       TAB_WIDTH,
        color:       DEFAULT_ACTIVE_COLOR,
    },
    {
        name:        'Сборка',
        displayName: ['СЗ:', 'Сборка'],
        component:   'Assembly',
        icon:        '💡',
        active:      false,
        width:       TAB_WIDTH,
        color:       DEFAULT_ACTIVE_COLOR,
    },
    {
        name:        'ПБ',
        displayName: ['СЗ:', 'ПБ'],
        component:   'Blocks',
        icon:        '💡',
        active:      false,
        width:       TAB_WIDTH,
        color:       DEFAULT_ACTIVE_COLOR,
    },
    {
        name:        'Расход',
        displayName: ['Расход', ''],
        component:   'Expense',
        icon:        '💡',
        active:      false,
        width:       TAB_WIDTH,
        color:       DEFAULT_ACTIVE_COLOR,
    },
    {
        name:        'Материалы',
        displayName: ['Материалы', ''],
        component:   'Materials',
        icon:        '💡',
        active:      false,
        width:       TAB_WIDTH,
        color:       DEFAULT_ACTIVE_COLOR,
    },
]

// __ Используем shallowRef для активного компонента.
// __ Это важно, так как мы будем хранить определение компонента,
// __ и нам не нужна глубокая реактивность для этого объекта.
const activeComponent = shallowRef<any | null>(null)
const activeTabName   = ref<string | null>(null)

// Объект для кэширования загруженных компонентов
const componentCache: Record<string, any> = {}

// __ Устанавливает активную вкладку и загружает соответствующий компонент.
const setActiveTab = (tab: ITab) => {
    activeTabName.value = tab.name

    if (componentCache[tab.name]) {
        activeComponent.value = componentCache[tab.name]
        console.log(`Использован кэшированный компонент: ${tab.name}`)
    } else {
        // Формируем ключ, который соответствует пути к файлу
        const filePath = `/resources/js/src/components/dashboard/orders/order_components/order_card/tabs/${tab.component}.vue`;
        activeComponent.value = defineAsyncComponent({
            // Просто передаем функцию из мапы
            loader: COMPONENTS[filePath] as () => Promise<any>
        })

        // activeComponent.value    = defineAsyncComponent({
        //
        //     loader: () => import(COMPONENTS[filePath] as any),
        //     // loader: () => import(`${path}/${componentName}.vue`),
        //     // loader: () => import(`${path}/${componentName}.vue`),
        //     // loader: () => import(PATH_TO_MAIN + workPath + workName),
        //     // Можно добавить loadingComponent, errorComponent, timeout и т.д.
        //     // loadingComponent: /* компонент-заглушка */,
        //     // errorComponent: /* компонент ошибки */,
        //     // delay: 200, // Задержка перед показом loadingComponent
        //     // timeout: 3000, // Таймаут загрузки
        // })
        // activeComponent.value    = loadAsyncComponent(tab.component, PATH_TO_COMPONENTS)
        componentCache[tab.name] = activeComponent.value
        console.log(`Активирована вкладка: ${tab.name}, загрузка компонента: ${tab.component}`)
    }
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


// __ Устанавливаем новую дату отгрузки
const patchLoadAt = async (newDate: Date) => {
    const result = await ordersStore.patchLoadAtDate(order.value?.id, formatDateTime(newDate))
    if (checkCRUD(result)) {
        if (order.value) {
            order.value.load_at = formatDateTime(newDate)
        }
    } else {
        await showError()
        return
    }
}


// __ Устанавливаем/редактируем Описание
const patchDescription = async (newText: string) => {
    const result = await ordersStore.patchDescription(order.value?.id, newText)
    if (checkCRUD(result)) {
        if (order.value) {
            order.value.description = newText.trim() !== '' ? newText.trim() : undefined
        }
    } else {
        await showError()
        return
    }
}

// __ Удаляем линию контекста в Заявке
const deleteOrderLine = async (inOrderLine: IRenderOrderLine) => {
    // console.log('orderLine: ', inOrderLine)

    // __ Находим строку
    const orderLine = order.value?.lines.find(line => line.id === inOrderLine.id)
    if (!orderLine) {
        return
    }

    // __ Проверка на присутствии строки в СЗ для проверки на статус
    // __ Нельзя удалить, если Выполено или Выполняется

    // __ Пошив
    let findTask = null
    for (const task of (order.value?.tasks.sewing_tasks || [])) {
        if (task.line_ids.includes(inOrderLine.id)) {
            findTask = task
            break
        }
    }

    if (findTask && [SEWING_TASK_STATUSES.RUNNING.ID, SEWING_TASK_STATUSES.DONE.ID].includes(findTask.status.status_id)) {
        modalInfoText.value = [
            'Строка:',
            `${orderLine.size} ${orderLine.model.name_report} ${orderLine.amount}`,
            'не может быть удалена.',
            `Она присутствует в СЗ Пошива от ${formatDateInFullFormat(findTask.action_at)}`,
            `со статусом ${findTask.status.display_name}`
        ]

        modalInfoType.value = 'danger'
        modalInfoMode.value = 'inform'
        await appModalAsyncMultiline.value!.show()
        return
    }

    // __ Раскрой
    findTask = null
    for (const task of (order.value?.tasks.cutting_tasks || [])) {
        if (task.line_ids.includes(inOrderLine.id)) {
            findTask = task
            break
        }
    }

    if (findTask && [CUTTING_TASK_STATUSES.RUNNING.ID, CUTTING_TASK_STATUSES.DONE.ID].includes(findTask.status.status_id)) {
        modalInfoText.value = [
            'Строка:',
            `${orderLine.size} ${orderLine.model.name_report} ${orderLine.amount}`,
            'не может быть удалена.',
            `Она присутствует в СЗ Раскроя от ${formatDateInFullFormat(findTask.action_at)}`,
            `со статусом ${findTask.status.display_name}`
        ]

        modalInfoType.value = 'danger'
        modalInfoMode.value = 'inform'
        await appModalAsyncMultiline.value!.show()
        return
    }

    modalInfoText.value = [
        'Строка:',
        `${orderLine.size} ${orderLine.model.name_report} ${orderLine.amount}`,
        'будет удалена.',
        'Продолжить?',
    ]

    modalInfoType.value = 'danger'
    modalInfoMode.value = 'confirm'

    const answer = await appModalAsyncMultiline.value!.show()
    if (answer) {

        const result = await ordersStore.deleteOrderLine(inOrderLine.id)


        if (checkCRUD(result)) {
            if (order.value) {
                order.value.lines = order.value.lines.filter(line => line.id !== inOrderLine.id)
                //!!! TODO WARNING!!! IMPORTANT!!! Сообщение о пересчете СЗ для Блоков

            }
        } else {
            await showError()
            return
        }
    }

}

// provide(OrderKey, computed(() => order.value))
// provide(IdKey, computed(() => paramId.value))
// provide('test', computed(() => order))

// __ Определяем динамически нужные события на нужных компонентах
const dynamicEvents = computed(() => {
    const events: any = {}

    // __ Вешаем событие только если активный компонент предполагает такую логику
    // __ Можно проверять по имени компонента или по активной вкладке
    if (activeTabName.value === TAB_NAME_COMMON) {
        events['patch-load-at']     = patchLoadAt
        events['patch-description'] = patchDescription
        return events
    }

    if (activeTabName.value === TAB_NAME_CONTEXT) {
        // console.log('deleteOrderLine: ', deleteOrderLine)
        events['delete-order-line'] = deleteOrderLine
        return events
    }


    if (activeTabName.value === 'Sewing') {

    }

    return events
})


// __ Подготавливаем данные
onMounted(async () => {
    isLoading.value      = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {

            await router.isReady().then(() => {
                paramId.value = parseInt(route.params.id as unknown as string, 10)
            })
            order.value = await ordersStore.getOrderById(paramId.value)

            console.log('order.value: ', order.value)
        },
        undefined,
        // false,
    )


    // __ Опционально: активировать первую вкладку при монтировании компонента
    if (tabs.length > 0) {
        setActiveTab(tabs[0])
    }

    isLoading.value = false
})

</script>

<style scoped>
    .shadow {
        @apply shadow-[0_0_15px_rgba(79,70,229,0.4)];
    }
</style>
