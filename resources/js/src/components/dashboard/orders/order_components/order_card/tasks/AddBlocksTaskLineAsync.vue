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

                    <!-- __ Блок -->
                    <div>
                        <div class="flex flex-col items-center mb-1"><span class="text-white font-semibold">Блок Пружинный:</span></div>
                        <AppLabelTS
                            :text="`${blockCode} ${blockName}`.trim()"
                            rounded="4"
                            text-size="mini"
                            type="primary"
                            width="w-[500px]"
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
                            width="w-[200px]"
                            @change="onAmountChange"
                        />
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
import { computed, ref, onMounted, /*watch,*/ } from 'vue'
import type { IColorTypes, IBlock } from '@/types'

import { useBlocksStore } from '@/stores/BlocksStore.ts'

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
    type?: IColorTypes
    width?: string
    height?: string
}

const props = withDefaults(defineProps<IProps>(), {
    type  : 'primary',
    width : 'min-w-[500px]',
    height: 'min-h-[400px]',
})

const blocksStore = useBlocksStore()

// __ Подготавливаем переменные
const BLOCK_PLACEHOLDER = 'Выберите Блок... (двойной клик)'
const blocks            = ref<IBlock[]>([])
const block             = ref<IBlock | null>(null)
const blockCode         = ref<string>('')
const blockName         = ref<string>(BLOCK_PLACEHOLDER)
const amount            = ref<number>(0)

// __ Получаем Сами Блоки
const getBlocks = async () => {
    const temp: IBlock[] = await blocksStore.getBlocks()
    blocks.value         = temp.toSorted((a, b) => a.name.localeCompare(b.name))
}

// __ Тип для модального окна выбора Коллекции
const selectedItems         = ref<ISelectableItem[]>([])
const selectedItemId        = ref()
const appModalAsyncSelectTS = ref<any>(null)

// __ Выбираем Блок
const selectBlock = async () => {
    selectedItems.value = blocks.value.map(block => ({ ...block, id: block.code_1c, description: block.code_1c }))

    const findItem       = blocks.value.find(b => b.name === block.value?.name)
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
            block : block.value,
            amount: amount.value,
        }
    }
})

onMounted(async () => {
    await getBlocks()
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
