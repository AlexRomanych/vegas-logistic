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
                        label="Код ткани из 1С"
                        mode="text"
                        placeholder="Укажите код..."
                        width="w-[180px]"
                    />

                    <!-- __ Название Ткани -->
                    <AppInputTextTS
                        id="name"
                        v-model:textValue.trim="v$.name.$model as unknown as string"
                        :errors="v$.name.$errors"
                        label="Название ткани"
                        mode="text"
                        placeholder="Укажите название ткани..."
                        width="w-[420px]"
                    />
                </div>

                <!-- __ Количество настилов -->
                <AppInputNumberSimpleTS
                    id="layers"
                    v-model:input-number.number="v$.layers.$model as unknown as number"
                    :errors="v$.layers.$errors"
                    :width="FIELD_WIDTH_LABEL"
                    label="Количество настилов"
                    mode="text"
                    placeholder="Укажите количество настилов..."
                />

                <div class="flex">
                    <!-- __ Ширина ткани -->
                    <AppInputNumberSimpleTS
                        id="width"
                        v-model:input-number.number="v$.width.$model as unknown as number"
                        :errors="v$.width.$errors"
                        :width="FIELD_WIDTH_LABEL"
                        label="Ширина ткани, см"
                        mode="text"
                        placeholder="Укажите ширину ткани..."
                    />

                    <!-- __ Рабочая Ширина ткани -->
                    <AppInputNumberSimpleTS
                        id="width"
                        v-model:input-number.number="v$.widthWork.$model as unknown as number"
                        :errors="v$.widthWork.$errors"
                        :width="FIELD_WIDTH_LABEL"
                        label="Рабочая ширина ткани, см"
                        mode="text"
                        placeholder="Укажите рабочую ширину ткани..."
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

                <!-- __ Описание Ткани -->
                <AppInputTextAreaSimpleTS
                    id="description"
                    v-model:text-value.trim="v$.description.$model as unknown as string"
                    :value="v$.description.$model"
                    :width="FIELD_WIDTH_CHECK_BOX"
                    label="Описание ткани"
                    placeholder="Заполните описание..."
                />

                <!-- __ К списку -->
                <div class="m-3 mt-5 flex justify-between">
                    <router-link :to="{ name: 'manufacture.cell.cutting.textiles' }">
                        <AppInputButton
                            id="returnButton"
                            func="button"
                            title="К списку тканей"
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
    IBlockCollection,
    ICheckboxData,
    ICheckboxDataItem,
    IColorTypes, ICuttingTextile,
} from '@/types'

import { computed, onMounted, ref, watch } from 'vue'

import { useRoute, useRouter } from 'vue-router'

import { useVuelidate } from '@vuelidate/core'
import { helpers, minLength, maxLength, minValue, required, numeric, integer } from '@vuelidate/validators'

import { useCuttingStore } from '@/stores/CuttingStore.ts'

import { CUTTING_TEXTILE_DRAFT } from '@/app/constants/cutting.ts'

import { checkCRUD } from '@/app/helpers/helpers_checks.ts'

import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'
import AppInputTextAreaSimpleTS from '@/components/ui/inputs/AppInputTextAreaSimpleTS.vue'
import AppInputTextTS from '@/components/ui/inputs/AppInputTextTS.vue'
import AppInputNumberSimpleTS from '@/components/ui/inputs/AppInputNumberSimpleTS.vue'
import AppCallout from '@/components/ui/callouts/AppCallout.vue'
import AppModalAsyncMultiline from '@/components/ui/modals/AppModalAsyncMultiline.vue'
import AppModalAsyncSelectTS from '@/components/ui/modals/AppModalAsyncSelectTS.vue'
import AppCheckboxTSReactive from '@/components/ui/checkboxes/AppCheckboxTSReactive.vue'


type ISelectableItem = IBlockCollection & { id: number; name: string };

const cuttingStore = useCuttingStore()

const route  = useRoute()
const router = useRouter()

const updateKey = 0

const FIELD_WIDTH_LABEL     = 'w-[300px]'
const FIELD_WIDTH_CHECK_BOX = 'w-[610px]'

const isLoading         = ref(false)
const isFormCorrect     = ref(false)
const editMode          = ref(false)         // определяем режим работы формы (редактирование или создание)
let paramCode1C: string = ''

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
const cuttingTextile = ref<ICuttingTextile>(CUTTING_TEXTILE_DRAFT)

// __ Подгружаем данные, если мы в режиме редактирования
const loadEntity = async (paramCode1C: string) => {
    if (editMode.value) {
        cuttingTextile.value = await cuttingStore.getCuttingTextile(paramCode1C) as ICuttingTextile
    } else {
        cuttingTextile.value = JSON.parse(JSON.stringify(CUTTING_TEXTILE_DRAFT))
    }
}


// __ Определяем объекты валидации
// const id = ref(-1)
const code_1c     = ref('')
const name        = ref('')
const layers      = ref(0)
const width       = ref(0)
const widthWork   = ref(0)
const active      = ref(true)
const description = ref('')

// __ Заполняем объекты валидации
const fillData = () => {
    code_1c.value     = cuttingTextile.value.code_1c
    name.value        = cuttingTextile.value.name
    layers.value      = cuttingTextile.value.layers
    width.value       = cuttingTextile.value.width
    widthWork.value   = cuttingTextile.value.width_work
    active.value      = cuttingTextile.value.active
    description.value = cuttingTextile.value.description ?? ''
}


// __ Определяем объект валидации
const verify = {
    code_1c,
    name,
    layers,
    width,
    widthWork,
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

    code_1c    : {
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
    name       : {
        $autoDirty: true,
        required  : helpers.withMessage(REQUIRED_MESSAGE, required),
        minLength : helpers.withMessage(
            `Минимальная длина названия - ${MIN_NAME_LENGTH} символов`,
            minLength(MIN_NAME_LENGTH),
        ),
    },
    layers     : {
        $autoDirty: true,
        minValue  : helpers.withMessage(`Поле должно быть больше или равно 1`, minValue(1)),
        required  : helpers.withMessage(REQUIRED_MESSAGE, required),
        numeric   : helpers.withMessage(`Поле должно содержать только цифры`, numeric),
        integer   : helpers.withMessage(`Поле должно быть целочисленным`, integer),
        // $lazy: true,
    },
    widthWork  : {
        $autoDirty: true,
        minValue  : helpers.withMessage(`Поле должно быть больше или равно 0`, minValue(0)),
        required  : helpers.withMessage(REQUIRED_MESSAGE, required),
        numeric   : helpers.withMessage(`Поле должно содержать только цифры`, numeric),
        integer   : helpers.withMessage(`Поле должно быть целочисленным`, integer),
        // $lazy: true,
    },
    width      : {
        $autoDirty: true,
        minValue  : helpers.withMessage(`Поле должно быть больше или равно 0`, minValue(0)),
        required  : helpers.withMessage(REQUIRED_MESSAGE, required),
        numeric   : helpers.withMessage(`Поле должно содержать только цифры`, numeric),
        integer   : helpers.withMessage(`Поле должно быть целочисленным`, integer),
        // $lazy: true,
    },
    description: {},
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

// __ Обработчик чекбокса на active
const activeCheckedHandler = (data: ICheckboxDataItem | ICheckboxDataItem[]) => {
    if (!Array.isArray(data)) {
        cuttingTextile.value.active = data.id === 1
        active.value                = cuttingTextile.value.active
    }
}


// __ Показываем сообщение об ошибке
// const showError = async (error: string | null = null) => {
//     modalInfoType.value = 'danger'
//     modalInfoMode.value = 'inform'
//     modalInfoText.value = error ? [error] : ['Упс! Что-то пошло не так!', 'Ошибка при обработке запроса!']
//     await appModalAsyncMultiline.value!.show()
// }

// __ Отправка формы
const formSubmit = async () => {

    isFormCorrect.value = await v$.value.$validate() // валидируем всю форму
    if (!isFormCorrect.value) return // это показатель ошибки

    cuttingTextile.value.code_1c     = code_1c.value
    cuttingTextile.value.name        = name.value
    cuttingTextile.value.active      = active.value
    cuttingTextile.value.description = description.value
    cuttingTextile.value.layers      = layers.value
    cuttingTextile.value.width       = width.value
    cuttingTextile.value.width_work  = widthWork.value

    console.log('cuttingTextile.value', cuttingTextile.value)

    let result

    if (!editMode.value) {
        result = await cuttingStore.createCuttingTextile(cuttingTextile.value)
    } else {
        result = await cuttingStore.updateCuttingTextile(cuttingTextile.value)
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
    () => layers,
    () => width,
    () => widthWork,
    () => description,
], async () => {
    isFormCorrect.value = await v$.value.$validate() // валидируем всю форму
}, { deep: true, immediate: true })

// __ Запускаем сразу валидацию формы
onMounted(async () => {
    // warn: Порядок важен!
    isLoading.value = true

    cuttingTextile.value = JSON.parse(JSON.stringify(CUTTING_TEXTILE_DRAFT))

    await router.isReady().then(() => {
        paramCode1C    = route.params.code_1c as unknown as string
        editMode.value = route.meta.mode === 'edit' // определяем режим работы формы (редактирование или создание)
    })

    await loadEntity(paramCode1C)

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
