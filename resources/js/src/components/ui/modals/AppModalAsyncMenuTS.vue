<template>
    <Teleport to="body">

        <div v-if="showModal"
             class="dark-container">

            <div :class="[width, height, borderColor, 'modal-container']">

                <!-- __ Крестик Закрыть -->
                <div class="close-cross-container">
                    <div class="m-1 p-1">
                        <AppInputButton
                            id="close"
                            :type="type"
                            height="w-5"
                            title="x"
                            width="w-[30px]"
                            @buttonClick="select(false)"
                        />
                    </div>
                </div>

                <!-- __ Меню -->
                <div class="flex flex-col w-[90%] px-6 gap-0.5 overflow-y-auto custom-scrollbar">
                    <div v-for="item of menu.data" :key="item.id" class="w-full" @click="selectMenuItem(item.id)">
                        <AppLabelMultiLineTS
                            :text="item.title"
                            :type="type"
                            align="center"
                            class="w-full cursor-pointer hover:opacity-90 transition-opacity"
                            height="min-h-[40px]"
                            rounded="4"
                            width="w-full"
                        />
                    </div>
                </div>


                <!-- __ Закрыть -->
                <div class="w-full flex justify-end px-6 mt-4">
                    <AppInputButton
                        id="confirm"
                        :type="type"
                        title="Закрыть"
                        @buttonClick="select(false)"
                    />
                </div>

            </div>
        </div>

    </Teleport>
</template>

<script lang="ts" setup>
import { computed, ref, } from 'vue'
import type { IModalAsyncMenu, IColorTypes, IModalAsyncMenuItem } from '@/types'

import { getColorClassByType } from '@/app/helpers/helpers.js'

import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'
import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'

interface IProps {
    menu: IModalAsyncMenu,
    type?: IColorTypes,
    width?: string,
    height?: string,
}

const props = withDefaults(defineProps<IProps>(), {
    type:   'primary',
    width:  'min-w-[500px]',
    height: 'min-h-[300px]',
})

const showModal = ref(false)           // реактивность видимости модального окна


// __ 1. Описываем структуру возвращаемых данных
interface IModalResponse {
    value: boolean
    menuItem: number | null // Разрешаем null, если ничего не выбрали
}

// 2. Типизируем Resolve функцию промиса
type ModalResolve = (result: IModalResponse) => void

// 3. Объявляем переменную
let resolvePromise: ModalResolve | null = null


// Определяем тип прямо над show
const show = (): Promise<IModalResponse> => {
    showModal.value = true
    selectedMenuItem.value = null // Сбрасываем выбор при каждом открытии

    return new Promise((resolve) => {
        resolvePromise = resolve
    })
}

const select = (value: boolean) => {
    if (resolvePromise) {
        // resolvePromise(value)
        resolvePromise({ value, menuItem: selectedMenuItem.value })  // будет возвращать в родитель не просто true/false, а нужные данные в форме объекта
        showModal.value = false
        resolvePromise  = null
    }
}


const selectedMenuItem = ref<null | number>(null)

const selectMenuItem   = (item: number) => {
    selectedMenuItem.value = item
    select(true)
}

defineExpose({
    show,
})

const borderColor = computed(() => getColorClassByType(props.type, 'border'))

</script>

<style scoped>

.modal-container {
    /* Добавляем py-4 для отступов сверху и снизу всей модалки */
    @apply bg-slate-800 bg-opacity-100 rounded-xl flex flex-col items-center border-l-8 py-4
}

/* Контейнер для списка меню */
.menu-list-container {
    @apply flex flex-col w-full px-8 gap-3 my-4;
}

.close-cross-container {
    /* Убираем h-full, чтобы крестик не растягивал все пространство */
    @apply flex justify-end w-full px-2
}

/* Если в AppLabelMultiLineTS внутри есть flex, убедитесь, что он занимает 100% */
:deep(.app-label-multiline) {
    width: 100% !important;
}


.dark-container {
    @apply z-[999] bg-slate-500 bg-opacity-95 fixed w-screen h-screen top-0 left-0 flex justify-center items-center
}


</style>
