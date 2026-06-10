<template>
    <div v-if="!isLoading" :key="updateKey" class="m-2 p-2 w-max-fit">

        <form @submit.prevent="formSubmit">

            <div class="border-2 rounded-lg border-slate-400 p-2 size-fit">

                <div class="flex gap-x-1">
                    <!-- __ Код из 1С -->
                    <AppInputTextTS
                        id="name"
                        v-model:textValue.trim="v$.code_1c.$model as unknown as string"
                        :errors="v$.code_1c.$errors"
                        label="Код группы из 1С"
                        mode="text"
                        placeholder="Укажите код..."
                        width="w-[180px]"
                    />

                    <!-- __ Название Коллекции -->
                    <AppInputTextTS
                        id="name"
                        v-model:textValue.trim="v$.name.$model as unknown as string"
                        :errors="v$.name.$errors"
                        label="Название группы блоков"
                        mode="text"
                        placeholder="Укажите название группы..."
                        width="w-[420px]"
                    />
                </div>

                <div class="flex">
                    <!-- __ Производительность -->
                    <AppInputNumberSimpleTS
                        id="productivity"
                        v-model:input-number.number="v$.productivity.$model as unknown as number"
                        :errors="v$.productivity.$errors"
                        :width="FIELD_WIDTH_LABEL"
                        label="Производительность, мп/ч"
                        mode="text"
                        placeholder="Производительность..."
                        step="0.001"
                    />

                    <!-- __ Приоритет выполнения -->
                    <AppInputNumberSimpleTS
                        id="priority"
                        v-model:input-number.number="v$.priority.$model as unknown as number"
                        :errors="v$.priority.$errors"
                        :width="FIELD_WIDTH_LABEL"
                        label="Приоритет выполнения"
                        mode="text"
                        placeholder="Укажите приоритет..."
                    />
                </div>

                <div class="flex">
                    <!-- __ Длина блока -->
                    <AppInputNumberSimpleTS
                        id="length"
                        v-model:input-number.number="v$.length.$model as unknown as number"
                        :errors="v$.length.$errors"
                        :width="FIELD_WIDTH_LABEL"
                        label="Длина блока, см"
                        mode="text"
                        placeholder="Укажите длину блока..."
                    />

                    <!-- __ Высота блока -->
                    <AppInputNumberSimpleTS
                        id="height"
                        v-model:input-number.number="v$.height.$model as unknown as number"
                        :errors="v$.height.$errors"
                        :width="FIELD_WIDTH_LABEL"
                        label="Высота блока, см"
                        mode="text"
                        placeholder="Укажите высоту блока..."
                    />
                </div>

                <!-- __ КДБ -->
                <div class="input-wrapper" @dblclick="selectKDB">
                    <AppInputTextTS
                        id="kdb"
                        v-model:textValue.trim="v$.kdb.$model as unknown as string"
                        :errors="v$.kdb.$errors"
                        :width="FIELD_WIDTH_CHECK_BOX"
                        class="disabled-input"
                        disabled
                        label="КДБ"
                        mode="text"
                        placeholder="Выберите КДБ (двойной клик)..."
                    />
                </div>

                <!-- __ Актуальность -->
                <div class="mt-5"></div>
                <AppCheckboxTSReactive
                    id="active"
                    :checkboxData="activeCheckboxData"
                    :width="FIELD_WIDTH_CHECK_BOX"
                    dir="horizontal"
                    inputType="radio"
                    legend="Актуальность записи"
                    type="secondary"
                    @checked="activeCheckedHandler"
                />

                <!-- __ Own - Собственное производство -->
                <div class="mt-5"></div>
                <AppCheckboxTSReactive
                    id="own"
                    :checkboxData="ownCheckboxData"
                    :width="FIELD_WIDTH_CHECK_BOX"
                    dir="horizontal"
                    inputType="radio"
                    legend="Собственное производство"
                    type="secondary"
                    @checked="ownCheckedHandler"
                />

                <!-- __ Линия производства -->
                <div class="mt-5"></div>
                <AppCheckboxTSReactive
                    id="line"
                    :checkboxData="lineCheckboxData"
                    :width="FIELD_WIDTH_CHECK_BOX"
                    dir="horizontal"
                    inputType="radio"
                    legend="Линия производства"
                    type="secondary"
                    @checked="lineCheckedHandler"
                />

                <!-- __ Альтернативная Линия производства -->
                <div class="mt-5"></div>
                <AppCheckboxTSReactive
                    id="line-alt"
                    :checkboxData="lineAltCheckboxData"
                    :width="FIELD_WIDTH_CHECK_BOX"
                    dir="horizontal"
                    inputType="radio"
                    legend="Альтернативная линия производства"
                    type="secondary"
                    @checked="lineAltCheckedHandler"
                />

                <!-- __ Единица измерения -->
                <div class="mt-5"></div>
                <AppCheckboxTSReactive
                    id="calc-mode"
                    :checkboxData="unitCheckboxData"
                    :width="FIELD_WIDTH_CHECK_BOX"
                    dir="horizontal"
                    inputType="radio"
                    legend="Единица измерения"
                    type="secondary"
                    @checked="unitCheckedHandler"
                />

                <!-- __ Описание операции -->
                <AppInputTextAreaSimpleTS
                    id="description"
                    v-model:text-value.trim="v$.description.$model as unknown as string"
                    :value="v$.description.$model"
                    :width="FIELD_WIDTH_CHECK_BOX"
                    label="Описание операции"
                    placeholder="Заполните описание..."
                />

                <!-- __ К списку -->
                <div class="m-3 mt-5 flex justify-between">
                    <router-link :to="{ name: 'manufacture.cell.blocks.collections.show' }">
                        <AppInputButton
                            id="returnButton"
                            func="button"
                            title="К списку групп"
                            width="w-[230px]"
                        />
                    </router-link>

                    <!--<AppInputButton-->
                    <!--    id="resetButton"-->
                    <!--    func="reset"-->
                    <!--    title="Сброс"-->
                    <!--    type="danger"-->
                    <!--    width="w-[150px]"-->
                    <!--/>-->

                    <!-- __ Сохранить -->
                    <AppInputButton
                        id="submitButton"
                        :type="isFormCorrect ? 'success' : 'danger'"
                        func="submit"
                        title="Сохранить"
                        width="w-[230px]"
                    />
                </div>

            </div>

        </form>

    </div>

    <!-- __ Модальное окно для сообщений -->
    <AppModalAsyncMultiline
        ref="appModalAsyncMultiline"
        :mode="modalInfoMode"
        :text="modalInfoText"
        :type="modalInfoType"
    />

    <!-- __ Выпадающий список-->
    <AppModalAsyncSelectTS
        ref="appModalAsyncSelectTS"
        v-model="selectedItemId"
        :items="selectedItems"
        title="Выберите данные"
        width="w-[600px]"
    />

    <AppCallout
        :show="calloutShow"
        :text="calloutMessage"
        :type="calloutType"
        @toggleShow="calloutHandler"
    />

</template>

<script lang="ts" setup>
import type {
    IBlockCollection, IBlockDocument,
    IBlockLine,
    IBlockUnit,
    ICheckboxData,
    ICheckboxDataItem,
    IColorTypes,
} from '@/types'

import { computed, onMounted, ref, watch } from 'vue'

import { useRoute, useRouter } from 'vue-router'

import { useVuelidate } from '@vuelidate/core'
import { helpers, minLength, maxLength, minValue, required, numeric, integer } from '@vuelidate/validators'

import { useBlocksStore } from '@/stores/BlocksStore'
import { useSharedStore } from '@/stores/SharedStore.ts'

import { BLOCK_COLLECTION_DRAFT, LINE_1, LINE_2, UNIT_PICS, UNIT_METERS, LINE_0 } from '@/app/constants/blocks.ts'

import { checkCRUD } from '@/app/helpers/helpers_checks.ts'

import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'
import AppInputTextAreaSimpleTS from '@/components/ui/inputs/AppInputTextAreaSimpleTS.vue'
import AppInputTextTS from '@/components/ui/inputs/AppInputTextTS.vue'
import AppInputNumberSimpleTS from '@/components/ui/inputs/AppInputNumberSimpleTS.vue'
import AppCallout from '@/components/ui/callouts/AppCallout.vue'
import AppModalAsyncMultiline from '@/components/ui/modals/AppModalAsyncMultiline.vue'
import AppModalAsyncSelectTS from '@/components/ui/modals/AppModalAsyncSelectTS.vue'
import AppCheckboxTSReactive from '@/components/ui/checkboxes/AppCheckboxTSReactive.vue'
// import AppCheckboxTS from '@/components/ui/checkboxes/AppCheckboxTS.vue'


type ISelectableItem = IBlockDocument & { id: number; name: string };

const blockStore  = useBlocksStore()
const sharedStore = useSharedStore()

const route  = useRoute()
const router = useRouter()

const updateKey = 0

const FIELD_WIDTH_LABEL     = 'w-[300px]'
const FIELD_WIDTH_CHECK_BOX = 'w-[610px]'

const isLoading     = ref(false)
const isFormCorrect = ref(false)
const editMode      = ref(false)         // определяем режим работы формы (редактирование или создание)
let paramId: number = -1

const calloutShow    = ref(false)       // состояние окна
const confirmClick   = ref(false)       // определяем для вывода этого callout
const calloutMessage = ref('')          // определяем показываемое сообщение
const calloutType    = ref('danger')    // определяем тип callout
const calloutHandler = () => setInterval(() => (confirmClick.value = false), 5000)

// __ Тип для модального окна Сообщений
const modalInfoType          = ref<IColorTypes>('danger')
const modalInfoText          = ref<string | string[]>('')
const modalInfoMode          = ref<'inform' | 'confirm'>('confirm')
const appModalAsyncMultiline = ref<InstanceType<typeof AppModalAsyncMultiline> | null>(null)

// __ Тип для модального окна выбора КДБ
const selectedItems         = ref<ISelectableItem[]>([])
const selectedItemId        = ref()
const appModalAsyncSelectTS = ref<any>(null)

// __ Подготавливаем переменные
const blockDocs       = ref<IBlockDocument[]>([])
const blockCollection = ref<IBlockCollection>(BLOCK_COLLECTION_DRAFT)
// let activeCheckboxData: ICheckboxData  // выбор активности
// let ownCheckboxData: ICheckboxData     // выбор собственного производства
// let lineCheckboxData: ICheckboxData    // выбор линии
// let lineAltCheckboxData: ICheckboxData // выбор альтернативной линии
// let unitCheckboxData: ICheckboxData    // выбор единицы измерения

// __ Подгружаем данные по коллекции, если мы в режиме редактирования
const loadEntity = async (paramId: number) => {
    if (editMode.value) {
        blockCollection.value = await blockStore.getBlockCollectionById(paramId) as IBlockCollection // Получаем Операцию
    } else {
        blockCollection.value = JSON.parse(JSON.stringify(BLOCK_COLLECTION_DRAFT))
    }
}

// __ Получаем КДБ
const getBlockDocs = async () => {
    blockDocs.value = await sharedStore.getBlockDesignDocuments()
    blockDocs.value.push({
        id         : 0,
        kdb        : 'Нет КДБ',
        name       : 'Нет КДБ',
        file_path  : null,
        description: null,
    })
}


// __ Определяем объекты валидации
// const id = ref(-1)
const code_1c      = ref('')
const name         = ref('')
const unit         = ref<IBlockUnit>(UNIT_PICS)
const kdb          = ref('')
const line         = ref<IBlockLine>(LINE_1)
const lineAlt      = ref<IBlockLine>(LINE_0)
const priority     = ref(0)
const height       = ref(0)
const length       = ref(0)
const productivity = ref(0)
const active       = ref(true)
const own          = ref(true)
const description  = ref('')


// __ Заполняем объекты валидации
const fillData = () => {
    code_1c.value      = blockCollection.value.code_1c
    name.value         = blockCollection.value.name
    unit.value         = blockCollection.value.unit ?? UNIT_PICS
    kdb.value          = blockCollection.value.kdb ?? ''
    line.value         = blockCollection.value.line !== LINE_0 ? blockCollection.value.line : LINE_1
    lineAlt.value      = blockCollection.value.line_alt ?? LINE_0
    priority.value     = blockCollection.value.priority
    height.value       = blockCollection.value.height
    length.value       = blockCollection.value.length
    productivity.value = blockCollection.value.productivity
    active.value       = blockCollection.value.active
    own.value          = blockCollection.value.own
    description.value  = blockCollection.value.description ?? ''
}


// __ Определяем объект валидации
const verify = {
    code_1c,
    name,
    kdb,
    productivity,
    priority,
    height,
    length,
    description,
}

// __ Определяем правила валидации
const MIN_NAME_LENGTH  = 10
const CODE_1C_LENGTH   = 9
const REQUIRED_MESSAGE = 'Поле обязательно для заполнения'

const rules = {
    // id: {
    //     // $lazy: true,
    //     $autoDirty: true,
    //     // numeric: helpers.withMessage(`Поле должно содержать только цифры`, numeric),
    //     minValue: helpers.withMessage(`Поле должно быть больше или равно -1`, minValue(-1)),
    //     integer: helpers.withMessage(`Поле должно быть целочисленным`, integer),
    //     required: helpers.withMessage(REQUIRED_MESSAGE, required),
    // },

    code_1c     : {
        $autoDirty: true,
        required  : helpers.withMessage(REQUIRED_MESSAGE, required),
        minLength : helpers.withMessage(
            `Длина кода из 1С - ${CODE_1C_LENGTH} символов`,
            minLength(CODE_1C_LENGTH),
        ),
        maxLength : helpers.withMessage(
            `Длина кода из 1С - ${CODE_1C_LENGTH} символов`,
            maxLength(CODE_1C_LENGTH),
        ),
    },
    name        : {
        $autoDirty: true,
        required  : helpers.withMessage(REQUIRED_MESSAGE, required),
        minLength : helpers.withMessage(
            `Минимальная длина названия - ${MIN_NAME_LENGTH} символов`,
            minLength(MIN_NAME_LENGTH),
        ),
    },
    productivity: {
        $autoDirty: true,
        minValue  : helpers.withMessage(`Поле должно быть больше 0`, minValue(0.001)),
        required  : helpers.withMessage(REQUIRED_MESSAGE, required),
        numeric   : helpers.withMessage(`Поле должно содержать только цифры`, numeric),
        // integer   : helpers.withMessage(`Поле должно быть целочисленным`, integer),
        // $lazy: true,
    },
    priority    : {
        $autoDirty: true,
        minValue  : helpers.withMessage(`Поле должно быть больше или равно 0`, minValue(0)),
        required  : helpers.withMessage(REQUIRED_MESSAGE, required),
        numeric   : helpers.withMessage(`Поле должно содержать только цифры`, numeric),
        integer   : helpers.withMessage(`Поле должно быть целочисленным`, integer),
        // $lazy: true,
    },
    height      : {
        $autoDirty: true,
        minValue  : helpers.withMessage(`Поле должно быть больше или равно 1`, minValue(1)),
        required  : helpers.withMessage(REQUIRED_MESSAGE, required),
        numeric   : helpers.withMessage(`Поле должно содержать только цифры`, numeric),
        integer   : helpers.withMessage(`Поле должно быть целочисленным`, integer),
        // $lazy: true,
    },
    length      : {
        $autoDirty: true,
        minValue  : helpers.withMessage(`Поле должно быть больше или равно 1`, minValue(1)),
        required  : helpers.withMessage(REQUIRED_MESSAGE, required),
        numeric   : helpers.withMessage(`Поле должно содержать только цифры`, numeric),
        integer   : helpers.withMessage(`Поле должно быть целочисленным`, integer),
        // $lazy: true,
    },
    kdb         : {},
    description : {},
}

// __ Оборачиваем в объект
const v$ = useVuelidate(rules, verify)

// __ Заполняем селекты данными
const activeCheckboxData = computed<ICheckboxData>(() => ({
    name: 'activity',
    data: [
        { id: 1, name: 'Активная', checked: active.value },
        { id: 2, name: 'Архив', checked: !active.value },
    ],
}))

const ownCheckboxData = computed<ICheckboxData>(() => ({
    name: 'own',
    data: [
        { id: 1, name: 'Да', checked: own.value },
        { id: 2, name: 'Нет', checked: !own.value },
    ],
}))

const lineCheckboxData = computed<ICheckboxData>(() => ({
    name: 'line',
    data: [
        { id: 1, name: 'Линия 1', checked: line.value === LINE_1 },
        { id: 2, name: 'Линия 2', checked: line.value === LINE_2 },
    ]
}))

const lineAltCheckboxData = computed<ICheckboxData>(() => ({
    name: 'line_alt',
    data: [
        { id: 0, name: 'Нет', checked: lineAlt.value === LINE_0 },
        { id: 1, name: 'Линия 1', checked: lineAlt.value === LINE_1 },
        { id: 2, name: 'Линия 2', checked: lineAlt.value === LINE_2 },
    ]
}))

const unitCheckboxData = computed<ICheckboxData>(() => ({
    name: 'unit',
    data: [
        { id: 1, name: UNIT_PICS.toUpperCase(), checked: unit.value === UNIT_PICS },
        { id: 2, name: UNIT_METERS.toUpperCase(), checked: unit.value === UNIT_METERS },
    ]
}))


// __ Обработчик чекбокса на active
const activeCheckedHandler = (data: ICheckboxDataItem | ICheckboxDataItem[]) => {
    if (!Array.isArray(data)) {
        blockCollection.value.active = data.id === 1
        active.value                 = blockCollection.value.active
    }
}

// __ Обработчик чекбокса на own - Собственное производство
const ownCheckedHandler = (data: ICheckboxDataItem | ICheckboxDataItem[]) => {
    if (!Array.isArray(data)) {
        blockCollection.value.own = data.id === 1
        own.value                 = blockCollection.value.own
    }
}

// __ Обработчик чекбокса на Линии
const lineCheckedHandler = (data: ICheckboxDataItem | ICheckboxDataItem[]) => {
    if (!Array.isArray(data)) {
        switch (data.id) {
            case 1:
                blockCollection.value.line = LINE_1
                break
            case 2:
                blockCollection.value.line = LINE_2
                break
            default:
                blockCollection.value.line = LINE_0
        }
    }
    line.value = blockCollection.value.line

    // __ Сбрасываем в default альтернативную линию
    if (blockCollection.value.line === blockCollection.value.line_alt) {
        blockCollection.value.line_alt = LINE_0
        lineAlt.value = blockCollection.value.line_alt
    }

}

// __ Обработчик чекбокса на Альтернативной Линии
const lineAltCheckedHandler = (data: ICheckboxDataItem | ICheckboxDataItem[]) => {
    if (!Array.isArray(data)) {
        switch (data.id) {
            case 0:
                blockCollection.value.line_alt = LINE_0
                break
            case 1:
                blockCollection.value.line_alt = LINE_1
                break
            case 2:
                blockCollection.value.line_alt = LINE_2
                break
            default:
                blockCollection.value.line_alt = LINE_0
        }
        lineAlt.value = blockCollection.value.line_alt

        // __ Сбрасываем в default альтернативную линию
        if (line.value === lineAlt.value) {
            blockCollection.value.line_alt = LINE_0
            lineAlt.value = LINE_0
        }
    }
}

// __ Обработчик чекбокса на Единице измерения
const unitCheckedHandler = (data: ICheckboxDataItem | ICheckboxDataItem[]) => {
    if (!Array.isArray(data)) {
        blockCollection.value.unit = data.id === 1 ? UNIT_PICS : UNIT_METERS
        unit.value                 = blockCollection.value.unit
    }
}


// __ Показываем сообщение об ошибке
const showError = async (error: string | null = null) => {
    modalInfoType.value = 'danger'
    modalInfoMode.value = 'inform'
    modalInfoText.value = error ? [error] : ['Упс! Что-то пошло не так!', 'Ошибка при обработке запроса!']
    await appModalAsyncMultiline.value!.show()
}


// __ Выбираем КДБ для Коллекции блоков
const selectKDB = async () => {
    selectedItems.value  = blockDocs.value
    const findItem       = blockDocs.value.find(doc => doc.name === kdb.value)
    selectedItemId.value = findItem ? findItem.id : 0

    const answer = await appModalAsyncSelectTS.value!.show(selectedItemId.value)
    if (answer) {
        const selectedKDB = appModalAsyncSelectTS.value!.selected
        if (selectedKDB.id === 0) {
            kdb.value = ''
        } else {
            kdb.value = selectedKDB.kdb
        }
    }
}


// __ Отправка формы
const formSubmit = async () => {

    isFormCorrect.value = await v$.value.$validate() // валидируем всю форму
    if (!isFormCorrect.value) return // это показатель ошибки

    blockCollection.value.code_1c      = code_1c.value
    blockCollection.value.name         = name.value
    blockCollection.value.unit         = unit.value
    blockCollection.value.kdb          = kdb.value
    blockCollection.value.line         = line.value
    blockCollection.value.line_alt     = lineAlt.value !== LINE_0 ? lineAlt.value : null
    blockCollection.value.priority     = priority.value
    blockCollection.value.height       = height.value
    blockCollection.value.length       = length.value
    blockCollection.value.productivity = productivity.value
    blockCollection.value.active       = active.value
    blockCollection.value.own          = own.value
    blockCollection.value.description  = description.value

    console.log('blockCollection.value', blockCollection.value)

    let result

    if (!editMode.value) {
        result = await blockStore.createBlockCollection(blockCollection.value)
    } else {
        result = await blockStore.updateBlockCollection(blockCollection.value)
    }

    if (checkCRUD(result.data)) {
        calloutMessage.value = result.payload
        calloutType.value    = 'success'
    } else {
        // await showError()
        calloutMessage.value = result.error
        calloutType.value    = 'danger'
    }

    confirmClick.value = true   // подтверждаем клик
    calloutShow.value  = true   // показываем callout
    calloutHandler()            // запускаем таймер на скрытие callout
}

watch([
    () => code_1c,
    () => name,
    () => kdb,
    () => productivity,
    () => priority,
    () => height,
    () => description,
], async () => {
    isFormCorrect.value = await v$.value.$validate() // валидируем всю форму
}, { deep: true, immediate: true })

// __ Запускаем сразу валидацию формы
onMounted(async () => {
    // warn: Порядок важен!
    isLoading.value = true

    blockCollection.value = JSON.parse(JSON.stringify(BLOCK_COLLECTION_DRAFT))

    await router.isReady().then(() => {
        paramId        = route.params.id as unknown as number
        editMode.value = route.meta.mode === 'edit' // определяем режим работы формы (редактирование или создание)
    })

    await Promise.all([loadEntity(paramId), getBlockDocs()])

    // await loadEntity(paramId)

    fillData()

    v$.value.$touch()
    isFormCorrect.value = await v$.value.$validate() // валидируем всю форму
    isLoading.value     = false

    // console.log('editMode.value: ', editMode.value)
})

</script>

<style scoped>
.input-wrapper {
    display: inline-block; /* Чтобы обертка была по размеру инпута */
}

.disabled-input {
    /* Важно: заставляем курсор игнорировать инпут,
       тогда клик уйдет на .input-wrapper */
    pointer-events: none;
}
</style>
