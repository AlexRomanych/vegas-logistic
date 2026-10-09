<template>
    <Teleport to="body">
        <Transition name="modal">
            <div v-if="showModal"
                 class="dark-container">

                <div :class="[width, height, borderColor, 'modal-container']">

                    <!-- __ Шапка с кнопкой закрытия -->
                    <div class="flex justify-end w-full shrink-0">
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

                    <div class="flex">
                        <!-- __ Блок -->
                        <div>
                            <div class="flex flex-col items-center mb-1"><span class="text-white font-semibold">Блок Пружинный:</span></div>
                            <AppLabelTS
                                :text="`${blockCode} ${blockName}`.trim()"
                                rounded="4"
                                text-size="mini"
                                type="primary"
                                width="w-[350px]"
                                @dblclick="selectBlock"
                            />
                        </div>

                        <!-- __ Блок -->
                        <!--<div class="input-wrapper" @dblclick="selectBlock">-->
                        <!--    <AppInputTextTS-->
                        <!--        id="block-substitution"-->
                        <!--        v-model:textValue.trim="blockCode"-->
                        <!--        :width="DEFAULT_WIDTH"-->
                        <!--        class="disabled-input"-->
                        <!--        disabled-->
                        <!--        label="Блок"-->
                        <!--        mode="text"-->
                        <!--        placeholder="Выберите Блок..."-->
                        <!--        type="primary"-->
                        <!--    />-->
                        <!--</div>-->

                        <!-- __ Количество -->
                        <div>
                            <div class="flex flex-col items-center mb-1"><span class="text-white font-semibold">Количество:</span></div>
                            <AppInputNumberTSExtend
                                v-model="amount"
                                :allowNegative="false"
                                :precision="0"
                                align="center"
                                height="h-[30px]"
                                placeholder=""
                                rounded="4"
                                text-size="mini"
                                type="primary"
                                width="w-[150px]"
                                @change="onAmountChange"
                            />
                        </div>
                    </div>
                    <!-- __ Комментарий Сменного Задания -->
                    <div class="w-full">
                        <div class="flex flex-col items-center  mb-1 mt-3">
                            <span class="text-white font-semibold">Комментарий:</span>
                        </div>
                        <div class="relative group">
                                <textarea
                                    v-model="comment"
                                    class="w-full max-h-[100px] bg-[#161e2d] text-blue-400 text-sm font-mono leading-relaxed
                                           border border-slate-800/50 rounded-[4px] px-4 py-1
                                           focus:ring-1 focus:ring-blue-500 outline-none transition-all
                                           custom-scrollbar resize-none whitespace-pre-wrap"
                                    placeholder="Добавьте комментарий..."
                                    rows="8"
                                ></textarea>
                            <div class="absolute right-3 bottom-3 opacity-20 pointer-events-none">
                                <svg class="text-slate-400" fill="none" height="20" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                     stroke-width="2" viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg">
                                    <polyline points="16 18 22 12 16 6"></polyline>
                                    <polyline points="8 6 2 12 8 18"></polyline>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- __ Нижний блок с кнопками действий -->
                    <div class="w-full flex justify-end shrink-0 pt-2">
                        <div v-if="blockCode !== '' && amount > 0" class="m-1 p-1">
                            <AppInputButton
                                id="confirm"
                                :type="type"
                                title="Добавить"
                                @buttonClick="select(true)"
                            />
                        </div>

                        <div class="m-1 p-1">
                            <AppInputButton
                                id="cancel"
                                :type="type"
                                title="Отмена"
                                @buttonClick="select(false)"
                            />
                        </div>
                    </div>

                </div>
            </div>
        </Transition>
    </Teleport>

    <!-- __ Выпадающий список-->
    <AppModalAsyncSelectTS
        ref="appModalAsyncSelectTS"
        v-model="selectedItemId"
        :items="selectedItems"
        title="Выберите данные"
        width="w-[600px]"
    />
</template>

<script lang="ts" setup>
import { computed, ref } from 'vue'

import type { IColorTypes, IBlock } from '@/types'

import { getColorClassByType } from '@/app/helpers/helpers.js'

import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'
import AppModalAsyncSelectTS from '@/components/ui/modals/AppModalAsyncSelectTS.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppInputNumberTSExtend from '@/components/ui/inputs/AppInputNumberTSExtend.vue'

// __ Разрешаем id быть строкой или числом
type ISelectableItem = Omit<IBlock, 'id'> & {
    id: string | number
    name: string
}

interface IProps {
    blocks: IBlock[]
    type?: IColorTypes
    width?: string
    height?: string
}

const props = withDefaults(defineProps<IProps>(), {
    type  : 'primary',
    width : 'min-w-[500px]',
    height: 'min-h-[400px]',
})

// __ Подготавливаем переменные
const BLOCK_PLACEHOLDER = 'Выберите Блок... (двойной клик)'
const block             = ref<IBlock | null>(null)
const blockCode         = ref<string>('')
const blockName         = ref<string>(BLOCK_PLACEHOLDER)
const amount            = ref<number>(0)
const comment           = ref<string>('')

// __ Тип для модального окна выбора Коллекции
const selectedItems         = ref<ISelectableItem[]>([])
const selectedItemId        = ref()
const appModalAsyncSelectTS = ref<any>(null)

// __ Выбираем Блок
const selectBlock = async () => {
    selectedItems.value = props.blocks.map(block => ({ ...block, id: block.code_1c, description: block.code_1c }))

    const findItem       = props.blocks.find(b => b.name === block.value?.name)
    selectedItemId.value = findItem ? findItem.id : 0

    const answer = await appModalAsyncSelectTS.value!.show(selectedItemId.value)
    if (answer) {
        block.value     = appModalAsyncSelectTS.value!.selected
        blockCode.value = block.value?.code_1c || ''
        blockName.value = block.value?.name || BLOCK_PLACEHOLDER
    }
}

const onAmountChange = () => {
    // console.log('onAmountChange:', amount.value)
    // emits('changeManual', val)
    // return amount
}

const showModal = ref(false)

const borderColor = computed(() => getColorClassByType(props.type, 'border'))

let resolvePromise: ((value: boolean) => void) | null
const show = () => {
    showModal.value = true
    return new Promise((resolve) => {
        resolvePromise = resolve
    })
}

const select = (value: boolean) => {
    if (resolvePromise) {
        resolvePromise(value)
        showModal.value = false
        resolvePromise  = null
    }
}

defineExpose({
    show,
    get lineData() {
        return {
            block  : block.value,
            amount : amount.value,
            comment: comment.value !== '' ? comment.value : null,
        }
    }
})

</script>

<style scoped>
.dark-container {
    @apply z-[999] bg-slate-500 bg-opacity-95 fixed w-screen h-screen top-0 left-0 flex justify-center items-center;
}

.modal-container {
    @apply bg-slate-800 bg-opacity-100 rounded-xl flex flex-col justify-between items-center border-l-8 p-4 box-border max-h-[90vh];
}

.text-container {
    @apply w-full flex-1 min-h-0 overflow-y-auto px-2 my-2;
}

.text-data {
    @apply w-full text-white space-y-1;
}

/* Кастомный темно-серый скроллбар */
.text-container::-webkit-scrollbar {
    width: 6px;
}

.text-container::-webkit-scrollbar-track {
    background: #1e293b;
    border-radius: 3px;
}

.text-container::-webkit-scrollbar-thumb {
    background: #475569;
    border-radius: 3px;
}

.text-container::-webkit-scrollbar-thumb:hover {
    background: #64748b;
}

/* Состояние появления и исчезновения */
.modal-enter-active,
.modal-leave-active {
    transition: all 0.5s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
    transform: scale(1.10);
}
</style>
