<template>
    <Teleport to="body">

        <div v-if="showModal && ready"
             class="dark-container">

            <div :class="[width, height, borderColor, 'modal-container']">

                <!-- __ Крестик закрытия окна -->
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

                <!-- __ Информационный текст -->
                <div class="m-2">
                    <AppLabelMultiLine
                        :text="displayTextArray"
                        :type="type"
                        :width="width"
                        align="center"
                    />
                </div>

                <!-- __ Выбор причины -->
                <div class="m-2">
                    <AppSelect
                        :disabled="!selectSourceStatus"
                        :multiple="false"
                        :selectData="selectData"
                        :type="type"
                        :width="width"
                        label="Выберите причину:"
                        text-size="small"
                        @change="selectReason"
                    />
                </div>

                <!-- __ Кнопка выбора другого источника -->
                <div class="m-2 cursor-pointer">
                    <AppInputButton
                        id="change-select"
                        :title="selectSourceButtonText"
                        :type="type"
                        :width="width"
                        @click="changeSourceStatus"
                    />
                </div>

                <!-- __ Само поле для ввода -->
                <div class="m-2">
                    <AppInputTextAreaSimple
                        id="text-area"
                        v-model.trim="v$.targetText.$model"
                        :disabled="selectSourceStatus"
                        :errors="v$.targetText.$errors"
                        :placeholder="placeholder"
                        :rows=4
                        :type="type"
                        :value="v$.targetText.$model"
                        :width="width"
                        errors-type="warning"
                        height="h-[150px]"
                    />
                </div>

                <div class="w-full h-full flex justify-end">

                    <div v-if="mode === 'confirm'" class="m-1 p-1">
                        <AppInputButton
                            v-if="saveButtonState"
                            id="confirm"
                            :type="type"
                            title="Сохранить"
                            @buttonClick="select(true)"
                        />
                    </div>

                    <div class="m-1 p-1">

                        <AppInputButton
                            id="confirm"
                            :title="mode === 'confirm' ? 'Отмена' : 'Закрыть'"
                            :type="type"
                            @buttonClick="select(false)"
                        />

                    </div>

                </div>

            </div>

        </div>

    </Teleport>
</template>

<script lang="ts" setup>
import type { IReason, ISelectDataItem, ISelectData } from '@/types'

import { computed, onMounted, ref, watch } from 'vue'

import { useReasonStore } from '@/stores/ReasonsStore.ts'

import { useVuelidate } from '@vuelidate/core'
import { required, minLength, helpers } from '@vuelidate/validators'

import { type ColorName, /*colorsList*/} from '@/app/constants/colorsClasses.js'
import { getColorClassByType, log } from '@/app/helpers/helpers.js'

import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'
import AppInputTextAreaSimple from '@/components/ui/inputs/AppInputTextAreaSimple.vue'
import AppLabelMultiLine from '@/components/ui/labels/AppLabelMultiLine.vue'
import AppSelect from '@/components/ui/selects/AppSelect.vue'


const emits = defineEmits<{
    (e: 'select'): void
}>()
// const emits = defineEmits(['select'])

interface IProps {
    width?: string,
    height?: string,
    type?: ColorName,
    text?: string | string[],
    value?: string,
    placeholder?: string,
    mode?: 'inform' | 'confirm',
    group: number | null,
    category: number | null
}

const props = withDefaults(defineProps<IProps>(),{
    width: 'min-w-[500px]',
    height: 'min-h-[300px]',
    type: 'primary',
    text: 'This is a Modal Window. Type text here',
    value: '',
    placeholder: 'Введите текст...',
    mode: 'inform',
});



// const props = defineProps({
//     width: {
//         type: String,
//         required: false,
//         default: 'min-w-[500px]',
//     },
//     height: {
//         type: String,
//         required: false,
//         default: 'min-h-[300px]',
//     },
//     type: {
//         type: String,
//         required: false,
//         default: 'primary',
//         validator: (type: string) => colorsList.includes(type)
//     },
//     text: {
//         type: [String, Array],
//         required: false,
//         default: 'This is a Modal Window. Type text here',
//         validator: (text) => Array.isArray(text) || typeof text === 'string'
//     },
//     value: {                    // текст внутри самого area
//         type: String,
//         required: false,
//         default: '',
//     },
//     placeholder: {
//         type: String,
//         required: false,
//         default: 'Введите текст...',
//     },
//     mode: {
//         type: String,
//         required: false,
//         default: 'inform',
//         validator: (mode: string) => ['inform', 'confirm'].includes(mode)
//     },
//     group: {
//         type: [Number, null],
//         required: true,
//     },
//     category: {
//         type: [Number, null],
//         required: true,
//     }
// })


const reasonsStore = useReasonStore()

// __ Список причин
const reasons = ref<IReason[]>([])

// __ Получаем список причин, сортируем по имени
const getReasons = async () => {
    if (props.group && props.category) {
        reasons.value = await reasonsStore.getReasonsByCellsGroupAndReasonCategory(props.group, props.category)
        reasons.value = reasons.value.sort((a, b) => a.name.localeCompare(b.name))
    } else {
        reasons.value = []
    }
}


// __ Переменная готовности компонента
const ready = ref(false)        // реактивность готовности компонента, для того чтобы не показывать задержку асинхронного запроса

// __ Сама причина
const targetText = ref(props.value)     // реактивность текста сообщения в area


// __ Определяем объект валидации ______________________________________________
const verify = {
    targetText,
}

// __ Определяем правила валидации
const MIN_TEXT_LENGTH = 20
const REQUIRED_MESSAGE = 'Поле обязательно для заполнения'

const rules = {
    targetText: {
        $autoDirty: true,
        required: helpers.withMessage(REQUIRED_MESSAGE, required),
        minLength: helpers.withMessage(
            `Минимальная длина причины - ${MIN_TEXT_LENGTH} символов`,
            minLength(MIN_TEXT_LENGTH),
        ),
    },
}
// __ Оборачиваем в объект валидации
const v$ = useVuelidate(rules, verify)

// __ End of Vuelidate


// __ Подготавливаем данные для компонента AppSelect
const selectData = ref<ISelectData>({name: 'reasons', data: []})
const createSelectData = () => {
    if (props.group && props.category) {

        selectData.value.data = reasons.value.map((reason, index) => {
            return {
                id: reason.id,
                name: reason.name,
                selected: index === 0,  // по умолчанию выделяем первый элемент
                disabled: false,
            }
        })

        return
    }

    selectData.value.data = []
}


// __ Выбираем причину
const selectReason = (reasonItem: ISelectDataItem): void => {
    targetText.value = reasonItem.name
    // log(reasonItem)
}


// __ Переключатель между списком и текстом
const selectSourceStatus = ref(true)
const selectSourceButtonText = ref('Другая причина')
const changeSourceStatus = () => {
    selectSourceStatus.value = !selectSourceStatus.value
    selectSourceButtonText.value = selectSourceStatus.value ? 'Другая причина' : 'Выбор их списка'
    if (!selectSourceStatus.value) targetText.value = ''
}

// __ Сброс состояния
const resetState = () => {
    targetText.value = ''
    selectSourceStatus.value = true
    selectSourceButtonText.value = 'Другая причина'
    // ready.value = false
}

// __ Подготовка текста аннотации для отображения
const getDisplayText = (text: string | string[]) => Array.isArray(text) ? text : [text]
let displayTextArray = ref(getDisplayText(props.text as string))

// __ Реактивность видимости модального окна
const showModal = ref(false)


// __ Реактивность состояния кнопки "Сохранить" в зависимости от area
const getSaveButtonState = () => {
    // return targetText.value.trim() !== ''
    return !v$.value.targetText.$invalid
}
const saveButtonState = ref(getSaveButtonState())


// __ Получаем класс цвета
const borderColor = computed(() => getColorClassByType(props.type as ColorName, 'border'))

// __ Обработка асинхронности работы с модальным окном
let resolvePromise: ((value: unknown) => void) | null = null
const show = (initTextValue = '') => {                  // Передаем сюда значение для area, потому что может быть не определен props на момент вызова. Это своего рода подстраховка
    targetText.value = initTextValue                                    // для текстовой модели
    showModal.value = true
    return new Promise((resolve) => {
        resolvePromise = resolve
    })
}

const select = (value: unknown): void => {
    if (resolvePromise) {
        resolvePromise(value)
        // resolvePromise({value, text: targetText.value})    // возвращает объект, можно так возвращать результаты
        showModal.value = false
        resolvePromise = null
    }
}

// __ даем родителю доступ к методам и свойствам компонента
defineExpose({
    show,
    resetState,
    inputText: targetText,
})

// __ Следим за отображением текста в модальном окне
watch(() => props.text, (value) => {
    displayTextArray.value = getDisplayText(value as string)
})

// __ Следим за состоянием текста в area
watch(() => targetText.value, (newValue) => {
    saveButtonState.value = getSaveButtonState()
    log(newValue)
})

// __ Следим за инициализирующим значением
watch(() => props.value, (newValue) => {
    targetText.value = newValue

}, {deep: true, immediate: true})


// __ Следим за группой и категорией
watch([
    () => props.group,
    () => props.category,
], () => {
    getReasons().then(() => {
        createSelectData()
        ready.value = true
        // log('reasons on watch: ', reasons.value)
    })
}, {deep: true})

onMounted(() => {
    getReasons().then(() => {
        createSelectData()
        ready.value = true
        v$.value.$touch()
        // log('reasons on mounted: ', reasons.value)
    })

})

</script>


<style scoped>

.dark-container {
    @apply z-[999] bg-slate-500 bg-opacity-95 fixed w-screen h-screen top-0 left-0 flex justify-center items-center
}

.modal-container {
    @apply bg-slate-800 bg-opacity-100 rounded-xl flex flex-col justify-between items-center border-l-8
}

.close-cross-container {
    @apply flex justify-end w-full h-full
}

/*
.text-container {
    @apply flex items-end
}

.text-data {
    @apply border-2 border-slate-800 w-full h-full text-white
}

.close-button-container {
    @apply w-full h-full flex justify-end
}
*/
</style>
