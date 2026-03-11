<template>
    <div v-if="!isLoading" class="mt-2 ml-2">
        <div class="flex justify-start">
            <AppLabelMultiLineTS
                v-for="tab in tabs"
                :key="tab.name"
                :text="tab.displayName"
                :type="activeTabName === tab.name ? 'success' : 'dark'"
                :width="tab.width"
                align="center"
                @click="setActiveTab(tab)"
                rounded="4"
            />
        </div>

        <!-- __ Разделялка -->
        <TheDividerLineTS />

        <div>
            <Suspense v-if="activeComponent">
                <component :is="activeComponent" />
                <template #fallback>
                    <div>Загрузка компонента {{ activeTabName }}...</div>
                </template>
            </Suspense>
            <div v-else>Выберите вкладку для отображения содержимого.</div>
        </div>
    </div>
</template>

<script setup lang="ts">

import { onMounted, ref, watch, shallowRef, provide, computed } from 'vue'

import { useRoute, useRouter } from 'vue-router'
import type { IRenderOrder } from '@/types'

import { useOrdersStore } from '@/stores/OrdersStore'
import { useSewingStore } from '@/stores/SewingStore'

import { OrderKey, IdKey } from './order_components/order_card/injectionKeys'

// __ Loader
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers_render.ts'
import { loadAsyncComponent } from '@/app/composable/loadAsyncComponent.ts'
import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import TheDividerLineTS from '@/components/ui/dividers/TheDividerLineTS.vue'


interface ITab {
    name: string
    displayName: string | string[]
    component: string // Строковое имя компонента
    icon: string
    active: boolean // Это поле можно удалить, activeTabName будет управлять активностью
    width: string
}

export type IProvidedData = {
    order: IRenderOrder
    id: number
}


const route  = useRoute()
const router = useRouter()                 // Определяем роутер

const ordersStore = useOrdersStore()
const sewingStore = useSewingStore()


// __ Тут путь к папке с динамическими компонентами
const PATH_TO_COMPONENTS = 'components/dashboard/orders/order_components/order_card/tabs'

// __ Подготавливаем переменные
const order     = ref<IRenderOrder | null>(null)
const isLoading = ref(false)
const paramId   = ref<number>(-1)

// __ Определяем массив вкладок
const TAB_WIDTH    = 'w-[150px]'
const tabs: ITab[] = [
    {
        name:        'Общие',
        displayName: ['Общая', 'информация'],
        component:   'Common',
        icon:        '✨',
        active:      false,
        width:       TAB_WIDTH,
    },
    {
        name:        'Содержимое',
        displayName: ['Содержимое', 'Заявки'],
        component:   'Context',
        icon:        '✨',
        active:      false,
        width:       TAB_WIDTH,
    },
    {
        name:        'Закрой',
        displayName: ['СЗ:', 'Закрой'],
        component:   'Cutting',
        icon:        '🚀',
        active:      false,
        width:       TAB_WIDTH,
    },
    {
        name:        'Пошив',
        displayName: ['СЗ:', 'Пошив'],
        component:   'Sewing',
        icon:        '🚀',
        active:      false,
        width:       TAB_WIDTH,
    },
    {
        name:        'Сборка',
        displayName: ['СЗ:', 'Сборка'],
        component:   'Assembly',
        icon:        '💡',
        active:      false,
        width:       TAB_WIDTH,
    },
    {
        name:        'ПБ',
        displayName: ['СЗ:', 'ПБ'],
        component:   'Blocks',
        icon:        '💡',
        active:      false,
        width:       TAB_WIDTH,
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
        activeComponent.value    = loadAsyncComponent(tab.component, PATH_TO_COMPONENTS)
        componentCache[tab.name] = activeComponent.value
        console.log(`Активирована вкладка: ${tab.name}, загрузка компонента: ${tab.component}`)
    }
}


// // __ Подготавливаем к рендеру
// const prepareDotToRender = (dot: IDotCard | null): IDotCard | null => {
//     if (!dot) return null
//
//     // Добавляем поле collapsed для рендера шаблонов вывоза
//     dot.dots_logistic = dot.dots_logistic.map((item: IDotLogistic) => ({ ...item, collapsed: true }))
//     return dot
// }
//
// // __ Получаем данные по точке
// const getDot = async () => {
//     const tempDot = await CRUDStore.getEntityById('dots', parseInt(dotId.value)) as IDotCard
//     dot.value     = prepareDotToRender(tempDot)
// }


// const providedData = computed(() => ({ order: order.value, id: paramId.value } as IProvidedData))
// provide('providedData', computed(() => providedData.value))

provide(OrderKey, computed(() => order.value))
provide(IdKey, computed(() => paramId.value))

// __ Запускаем сразу валидацию формы
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

</style>
