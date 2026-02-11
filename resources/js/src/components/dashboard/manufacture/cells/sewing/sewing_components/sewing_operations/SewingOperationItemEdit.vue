<template>

    <Teleport to="body">
        <Transition name="modal">


            <div v-if="showModal"
                 class="dark-container"
                 @click.self="select(false)">


                <div :class="[width, height, borderColor, 'modal-container']">

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

                    <div class="w-full pl-8 border-b border-slate-800/50">
                        <h3 class="text-left text-slate-500 uppercase tracking-widest text-[10px] font-bold mb-1">
                            Детализация
                        </h3>
                        <h2 class="text-left text-white text-lg font-bold">
                            Детализация типовой операции
                        </h2>
                    </div>

                    <div class="p-4 max-h-[60vh] overflow-y-auto custom-scrollbar space-y-1">

                        <div v-for="(item, index) in shortData" :key="index"
                             class="flex items-center gap-x-8 px-4 py-1 rounded-lg hover:bg-[#161e2d] transition-colors group"
                        >
                        <span class="w-24 shrink-0 text-slate-500 text-[11px] uppercase font-semibold tracking-tight">
                            {{ item.label }}
                        </span>

                            <template v-if="item.key === 'ratio'">
                                <div class="relative flex items-center">
                                    <input
                                        v-model.number="editableData.ratio"
                                        class="bg-slate-900 text-blue-400 text-sm font-bold border border-slate-700
                                               rounded-lg pl-3 pr-8 py-1 focus:ring-1 focus:ring-blue-500 outline-none w-28"
                                        step="0.1"
                                        type="number"/>
                                    <!--<span class="absolute right-3 text-[10px] text-slate-500 font-bold uppercase">x</span>-->
                                </div>
                            </template>

                            <!--<template v-if="item.key === 'ratio'">-->
                            <!--    <input-->
                            <!--        v-model.number="editableData.ratio"-->
                            <!--        class="bg-slate-900 text-blue-400 text-sm font-medium border border-slate-700 rounded px-2 py-0.5 focus:outline-none focus:border-blue-500 w-24"-->
                            <!--        step="0.1"-->
                            <!--        type="number"-->
                            <!--    />-->
                            <!--</template>-->

                            <template v-else-if="item.key === 'present'">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input
                                        v-model="editableData.present"
                                        class="sr-only peer"
                                        type="checkbox"
                                    >
                                    <div class="w-9 h-5 bg-slate-700 peer-focus:outline-none rounded-full peer
                                                peer-checked:after:translate-x-full peer-checked:after:border-white
                                                after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                                after:bg-white after:border-gray-300 after:border after:rounded-full
                                                after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600">
                                    </div>
                                    <span class="ml-3 text-xs font-medium text-slate-400">
                                        {{ editableData.present ? 'Есть' : 'Нет' }}
                                    </span>
                                </label>
                            </template>


                            <!--<template v-else-if="item.key === 'present'">-->
                            <!--    <input-->
                            <!--        v-model="editableData.present"-->
                            <!--        type="checkbox"-->
                            <!--        class="w-4 h-4 rounded border-slate-700 bg-slate-900 text-blue-500 focus:ring-blue-600 focus:ring-offset-slate-800"-->
                            <!--    />-->
                            <!--</template>-->

                            <span v-else
                                  class="text-slate-200 text-sm font-medium group-hover:text-blue-400 transition-colors">
                                {{ item.value }}
                            </span>


                            <!--<span class="text-slate-200 text-sm font-medium group-hover:text-blue-400 transition-colors">-->
                            <!--    {{ item.value }}-->
                            <!--</span>-->
                        </div>

                        <template v-if="longData.length">
                            <div v-for="(item, index) in longData" :key="index">
                                <div class="mt-4 px-4 py-3 bg-[#161e2d] rounded-xl border border-slate-800/50">
                                    <div class="text-slate-500 text-[10px] uppercase font-bold tracking-widest mb-2">
                                        {{ item.label }}
                                    </div>
                                    <p class="text-slate-300 text-sm leading-relaxed whitespace-pre-wrap">
                                        {{ item.value }}
                                    </p>
                                </div>
                            </div>
                        </template>

                    </div>

                    <div class="w-full flex justify-end gap-2 p-2">
                        <AppInputButton
                            id="cancel"
                            title="Отмена"
                            type="stone"
                            @buttonClick="select(false)"
                        />
                        <AppInputButton
                            id="confirm"
                            :type="type"
                            title="Сохранить"
                            @buttonClick="select(true)"
                        />
                    </div>

                </div>
            </div>

        </Transition>
    </Teleport>


</template>

<script lang="ts" setup>
import { computed, nextTick, reactive, ref, } from 'vue'

import type { IColorTypes } from '@/app/constants/colorsClasses.js'

import { getColorClassByType } from '@/app/helpers/helpers.js'

import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'
import type {
    ISewingOperation, ISewingOperationSchema,
} from '@/types'

interface IProps {
    operation: ISewingOperation | null,
    schema: ISewingOperationSchema | null,

    type?: IColorTypes,
    width?: string,
    height?: string,
}

const props = withDefaults(defineProps<IProps>(), {
    type:   'primary',
    width:  'min-w-[600px] max-w-[600px]',
    height: 'min-h-[200px]',
})


// __ Состояние для редактирования
const editableData = reactive({
    ratio:   0,
    present: false
})


const showModal = ref(false)           // реактивность видимости модального окна

let resolvePromise: ((value: boolean) => void) | null

// __ Функция для установки данных в зависимости от наличия операции в схеме
const setEditableData = () => {
    if (!props.schema || !props.operation) {
        return
    }
    const findIndex = props.schema.operations.findIndex(item => item.id === props.operation.id)
    if (findIndex !== -1) {
        editableData.present = true
        editableData.ratio   = props.schema.operations[findIndex].pivot.ratio ?? 0
    } else {
        editableData.present = false
        editableData.ratio   = 0
    }
}

const show = async () => {

    await nextTick()        // !!! Нужен для того, чтобы были доступны props.operation и props.schema
    setEditableData()
    showModal.value = true

    return new Promise((resolve) => {
        resolvePromise = resolve
    })
}


// const show = (initialRatio: number = 0, isPresent: boolean = false) => {
//     // Инициализируем данные при открытии
//     editableData.ratio = initialRatio
//     editableData.present = isPresent
//
//     showModal.value = true
//     return new Promise((resolve) => {
//         resolvePromise = resolve
//     })
// }


const select = (value: boolean) => {
    if (resolvePromise) {
        resolvePromise(value)
        showModal.value = false
        resolvePromise  = null
    }
}

defineExpose({
    show,
    get present() {
        return editableData.present
    },
    get ratio() {
        return editableData.ratio
    }
})

const borderColor = computed(() => getColorClassByType(props.type, 'border'))

// const ratio   = ref(0)
// const present = ref(false)

const shortData = computed(() => [
    { label: 'Схема', value: props.schema.name },
    { label: 'Операция', value: props.operation.name },
    { label: 'Оборудование', value: props.operation.machine },
    { label: 'Время операции', value: `${props.operation.time.toString()} сек.` },
    { label: 'Тип операции', value: props.operation.type === 'dynamic' ? 'Динамическая' : 'Статическая' },
    { label: 'Коэффициент', value: '', key: 'ratio' },      // Добавили key
    { label: 'Есть в схеме', value: '', key: 'present' },   // Добавили key
])

const longData = computed(() => [
    // { label: 'Состав', value: '123' },
    // { label: 'Примечание 1', value: '456' },
] as { label: string, value: string }[])


</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    @apply bg-slate-700 rounded-full;
}

/* Красивый перенос длинных слов, если они встретятся */
p {
    word-break: break-word;
}

.dark-container {
    @apply z-[999] bg-slate-500 bg-opacity-95 fixed w-screen h-screen top-0 left-0 flex justify-center items-center
}

.modal-container {
    @apply bg-slate-800 bg-opacity-100 rounded-xl flex flex-col justify-between border-l-8
}

.close-cross-container {
    @apply flex justify-end w-full h-full
}


/* Состояние появления и исчезновения */
.modal-enter-active,
.modal-leave-active {
    transition: all 0.5s ease;
}

/* Стартовое состояние при появлении / Финальное при исчезновении */
.modal-enter-from,
.modal-leave-to {
    opacity: 0;
    transform: scale(1.10); /* Легкое увеличение для эффекта приближения */
}
</style>



