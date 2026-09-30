<template>
    <div v-if="!isLoading" :key="updateKey" class="m-2 p-2 w-max-fit">

        <form @submit.prevent="formSubmit">

            <div class="border-2 rounded-lg border-slate-400 p-2 size-fit">

                <div class="flex gap-x-1">
                    <!-- __ Код из 1С -->
                    <AppInputTextTS
                        id="code_1c"
                        v-model:textValue.trim="v$.code_1c.$model as unknown as string"
                        :errors="v$.code_1c.$errors"
                        label="Код группы из 1С"
                        mode="text"
                        placeholder="Укажите код..."
                        width="w-[180px]"
                    />

                    <!-- __ Название Блока -->
                    <AppInputTextTS
                        id="name"
                        v-model:textValue.trim="v$.name.$model as unknown as string"
                        :errors="v$.name.$errors"
                        label="Название блока"
                        mode="text"
                        placeholder="Укажите название блока..."
                        width="w-[420px]"
                    />
                </div>

                <!-- __ Коллекция блоков -->
                <div class="input-wrapper" @dblclick="selectCollection">
                    <AppInputTextTS
                        id="block-collections"
                        v-model:textValue.trim="collectionName"
                        :errors="v$.collection.$errors"
                        :width="FIELD_WIDTH_CHECK_BOX"
                        class="disabled-input"
                        disabled
                        label="Группа блоков"
                        mode="text"
                        placeholder="Выберите Группу блоков (двойной клик)..."
                    />
                </div>

                <!--<div class="input-wrapper" @dblclick="selectCollection">-->
                <!--    <AppInputTextTS-->
                <!--        id="kdb"-->
                <!--        v-model:textValue.trim="v$.collection.$model as unknown as string"-->
                <!--        :errors="v$.collection.$errors"-->
                <!--        :width="FIELD_WIDTH_CHECK_BOX"-->
                <!--        class="disabled-input"-->
                <!--        disabled-->
                <!--        label="Группа блоков"-->
                <!--        mode="text"-->
                <!--        placeholder="Выберите Группу блоков (двойной клик)..."-->
                <!--    />-->
                <!--</div>-->

                <div class="flex">
                    <!-- __ Ширина блока -->
                    <AppInputNumberSimpleTS
                        id="width"
                        v-model:input-number.number="v$.width.$model as unknown as number"
                        :errors="v$.width.$errors"
                        :width="FIELD_WIDTH_LABEL"
                        label="Ширина блока, см"
                        mode="text"
                        placeholder="Укажите ширину блока..."
                    />

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

                <!-- __ Shown -->
                <div class="mt-5"></div>
                <AppCheckboxTSReactive
                    id="shown"
                    :checkboxData="shownCheckboxData"
                    :width="FIELD_WIDTH_CHECK_BOX"
                    dir="horizontal"
                    inputType="radio"
                    legend="Отображение в Справочнике"
                    type="secondary"
                    @checked="shownCheckedHandler"
                />

                <!-- __ Подменять ли при загрузке или нет (Substitution) -->
                <div class="mt-5"></div>
                <AppCheckboxTSReactive
                    id="substitution"
                    :checkboxData="substitutionCheckboxData"
                    :width="FIELD_WIDTH_CHECK_BOX"
                    dir="horizontal"
                    inputType="radio"
                    legend="Подмена при создании СЗ"
                    type="secondary"
                    @checked="substitutionCheckedHandler"
                />

                <!-- __ Блок для подмены -->
                <div class="input-wrapper" @dblclick="selectBlock">
                    <AppInputTextTS
                        id="block-substitution"
                        v-model:textValue.trim="blockName"
                        :errors="v$.blockSubstitutionName.$errors"
                        :width="FIELD_WIDTH_CHECK_BOX"
                        class="disabled-input"
                        disabled
                        label="Блок подмены"
                        mode="text"
                        placeholder="Выберите Блок подмены (двойной клик)..."
                    />
                </div>

                <!-- __ Описание Блока -->
                <AppInputTextAreaSimpleTS
                    id="description"
                    v-model:text-value.trim="v$.description.$model as unknown as string"
                    :value="v$.description.$model"
                    :width="FIELD_WIDTH_CHECK_BOX"
                    label="Описание блока"
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
    IBlock,
    IBlockCollection,
    ICheckboxData,
    ICheckboxDataItem,
    IColorTypes,
} from '@/types'

import { computed, onMounted, ref, watch } from 'vue'

import { useRoute, useRouter } from 'vue-router'

import { useVuelidate } from '@vuelidate/core'
import { helpers, minLength, maxLength, minValue, required, numeric, integer } from '@vuelidate/validators'

import { useBlocksStore } from '@/stores/BlocksStore'

import { BLOCK_DRAFT } from '@/app/constants/blocks.ts'

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


type ISelectableItem = (IBlockCollection | IBlock) & { id: number; name: string }

const blockStore = useBlocksStore()

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

// __ Тип для модального окна выбора Коллекции
const selectedItems         = ref<ISelectableItem[]>([])
const selectedItemId        = ref()
const appModalAsyncSelectTS = ref<any>(null)

// __ Подготавливаем переменные
const blockCollections = ref<IBlockCollection[]>([])
const blocks           = ref<IBlock[]>([])
const block            = ref<IBlock>(BLOCK_DRAFT)


// __ Подгружаем данные по коллекции, если мы в режиме редактирования
const loadEntity = async (paramId: number) => {
    if (editMode.value) {
        block.value = await blockStore.getBlockById(paramId) as IBlock // Получаем Операцию
    } else {
        block.value = JSON.parse(JSON.stringify(BLOCK_DRAFT))
    }
}

// __ Получаем Коллекции блоков
const getBlockCollections = async () => {
    blockCollections.value = await blockStore.getBlockCollections()
}

// __ Получаем Сами Блоки
const getBlocks = async () => {
    const temp: IBlock[] = await blockStore.getBlocks()
    blocks.value = temp.toSorted((a, b) => a.name.localeCompare(b.name))
}

// __ Определяем объекты валидации
// const id = ref(-1)
const code_1c               = ref('')
const name                  = ref('')
const collection            = ref('')
const blockSubstitutionName = ref('')
const blockSubstitutionCode = ref('')
const width                 = ref(0)
const length                = ref(0)
const active                = ref(true)
const shown                 = ref(true)
const substitution          = ref(true)
const description           = ref('')

const collectionName = computed(() => {
    const blockCollection = blockCollections.value.find(coll => coll.code_1c === collection.value)
    return blockCollection?.name ?? 'Не определено'
})

const blockName = computed(() => {
    const block = blocks.value.find(block => block.code_1c === blockSubstitutionCode.value)
    return block?.name ?? 'Не определен'
})

// __ Заполняем объекты валидации
const fillData = () => {
    code_1c.value               = block.value.code_1c
    name.value                  = block.value.name
    width.value                 = block.value.width
    length.value                = block.value.length
    collection.value            = block.value.collection
    blockSubstitutionName.value = block.value.substitution?.name ?? 'Не определен'
    blockSubstitutionCode.value = block.value.substitution?.code_1c ?? ''
    active.value                = block.value.active
    shown.value                 = block.value.shown
    substitution.value          = block.value.substitution?.substitution ?? false
    description.value           = block.value.description ?? ''
}


// __ Определяем объект валидации
const verify = {
    code_1c,
    name,
    width,
    length,
    collection,
    blockSubstitutionName,
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

    code_1c              : {
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
    name                 : {
        $autoDirty: true,
        required  : helpers.withMessage(REQUIRED_MESSAGE, required),
        minLength : helpers.withMessage(
            `Минимальная длина названия - ${MIN_NAME_LENGTH} символов`,
            minLength(MIN_NAME_LENGTH),
        ),
    },
    width                : {
        $autoDirty: true,
        minValue  : helpers.withMessage(`Поле должно быть больше или равно 1`, minValue(1)),
        required  : helpers.withMessage(REQUIRED_MESSAGE, required),
        numeric   : helpers.withMessage(`Поле должно содержать только цифры`, numeric),
        integer   : helpers.withMessage(`Поле должно быть целочисленным`, integer),
        // $lazy: true,
    },
    length               : {
        $autoDirty: true,
        minValue  : helpers.withMessage(`Поле должно быть больше или равно 1`, minValue(1)),
        required  : helpers.withMessage(REQUIRED_MESSAGE, required),
        numeric   : helpers.withMessage(`Поле должно содержать только цифры`, numeric),
        integer   : helpers.withMessage(`Поле должно быть целочисленным`, integer),
        // $lazy: true,
    },
    collection           : {},
    blockSubstitutionName: {},
    description          : {},
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

const shownCheckboxData = computed<ICheckboxData>(() => ({
    name: 'shown',
    data: [
        { id: 1, name: 'Отображать', checked: shown.value },
        { id: 2, name: 'Скрыть', checked: !shown.value },
    ],
}))

const substitutionCheckboxData = computed<ICheckboxData>(() => ({
    name: 'substitution',
    data: [
        { id: 1, name: 'Подменять', checked: substitution.value },
        { id: 2, name: 'Не подменять', checked: !substitution.value },
    ],
}))

// __ Обработчик чекбокса на active
const activeCheckedHandler = (data: ICheckboxDataItem | ICheckboxDataItem[]) => {
    if (!Array.isArray(data)) {
        block.value.active = data.id === 1
        active.value       = block.value.active
    }
}

// __ Обработчик чекбокса на shown
const shownCheckedHandler = (data: ICheckboxDataItem | ICheckboxDataItem[]) => {
    if (!Array.isArray(data)) {
        block.value.shown = data.id === 1
        shown.value       = block.value.shown
    }
}

// __ Обработчик чекбокса на substitution
const substitutionCheckedHandler = (data: ICheckboxDataItem | ICheckboxDataItem[]) => {
    if (!Array.isArray(data)) {
        if (data.id === 1) {
            substitution.value       = true
            block.value.substitution = {
                substitution: true,
                code_1c     : '',
                name        : '',
            }
        } else {
            substitution.value       = false
            block.value.substitution = null
        }
    }
}

// __ Показываем сообщение об ошибке
// const showError = async (error: string | null = null) => {
//     modalInfoType.value = 'danger'
//     modalInfoMode.value = 'inform'
//     modalInfoText.value = error ? [error] : ['Упс! Что-то пошло не так!', 'Ошибка при обработке запроса!']
//     await appModalAsyncMultiline.value!.show()
// }


// __ Выбираем Коллекцию блоков
const selectCollection = async () => {
    selectedItems.value  = blockCollections.value
    const findItem       = blockCollections.value.find(blockCollection => blockCollection.code_1c === collection.value)
    selectedItemId.value = findItem ? findItem.id : 0

    const answer = await appModalAsyncSelectTS.value!.show(selectedItemId.value)
    if (answer) {
        const selectedCollection = appModalAsyncSelectTS.value!.selected
        collection.value         = selectedCollection.code_1c
    }
}

// __ Выбираем Блок Подмены
const selectBlock = async () => {
    if (!substitution.value) {
        return
    }

    selectedItems.value  = blocks.value
    const findItem       = blocks.value.find(block => block.name === blockSubstitutionName.value)
    selectedItemId.value = findItem ? findItem.id : 0

    const answer = await appModalAsyncSelectTS.value!.show(selectedItemId.value)
    if (answer) {
        const selectedBlock         = appModalAsyncSelectTS.value!.selected
        blockSubstitutionCode.value = selectedBlock.code_1c
    }
}

// __ Отправка формы
const formSubmit = async () => {

    isFormCorrect.value = await v$.value.$validate() // валидируем всю форму
    if (!isFormCorrect.value) return // это показатель ошибки

    block.value.code_1c     = code_1c.value
    block.value.name        = name.value
    block.value.active      = active.value
    block.value.shown       = shown.value
    block.value.description = description.value
    block.value.width       = width.value
    block.value.length      = length.value
    block.value.collection  = collection.value

    if (substitution.value) {
        block.value.substitution = {
            substitution: true,
            code_1c     : blockSubstitutionCode.value,
            name        : blockSubstitutionName.value,
        }
    } else {
        block.value.substitution = null
    }

    console.log('block.value', block.value)

    let result

    if (!editMode.value) {
        result = await blockStore.createBlock(block.value)
    } else {
        result = await blockStore.updateBlock(block.value)
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
    () => width,
    () => length,
    () => description,
    () => collection,
], async () => {
    isFormCorrect.value = await v$.value.$validate() // валидируем всю форму
}, { deep: true, immediate: true })

// __ Запускаем сразу валидацию формы
onMounted(async () => {
    // warn: Порядок важен!
    isLoading.value = true

    block.value = JSON.parse(JSON.stringify(BLOCK_DRAFT))

    await router.isReady().then(() => {
        paramId        = route.params.id as unknown as number
        editMode.value = route.meta.mode === 'edit' // определяем режим работы формы (редактирование или создание)
    })

    await Promise.all([
        loadEntity(paramId),
        getBlockCollections(),
        getBlocks(),
    ])

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
    cursor: pointer;
}
</style>
