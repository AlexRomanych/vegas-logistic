<template>
    <div v-if="!isLoading" :key="updateKey" class="m-2 p-2 w-max-fit">

        <form @submit.prevent="formSubmit">

            <div class="border-2 rounded-lg border-slate-400 p-2 size-fit">

                <div class="flex gap-x-1">

                    <!-- __ Название Группы -->
                    <AppInputTextTS
                        id="name"
                        v-model:textValue.trim="v$.name.$model as unknown as string"
                        :errors="v$.name.$errors"
                        label="Название группы"
                        mode="text"
                        placeholder="Укажите название группы..."
                        :width="FIELD_WIDTH_LABEL"
                    />

                    <!-- __ Номер Группы -->
                    <AppInputNumberSimpleTS
                        id="layers"
                        v-model:input-number.number="v$.groupNumber.$model as unknown as number"
                        :errors="v$.groupNumber.$errors"
                        :width="FIELD_WIDTH_LABEL"
                        label="Номер Группы"
                        mode="text"
                        placeholder="Укажите Номер Группы..."
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
                    label="Описание Группы"
                    placeholder="Заполните описание..."
                />

                <!-- __ К списку -->
                <div class="m-3 mt-5 flex justify-between">
                    <router-link :to="{ name: 'manufacture.cell.assembly.model.manufacture.groups.show' }">
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

    <AppCallout
        :show="calloutShow"
        :text="calloutMessage"
        :type="calloutType"
        @toggleShow="calloutHandler"
    />

</template>

<script lang="ts" setup>
import type {
    IAssemblyModelManufactureGroup,
    ICheckboxData,
    ICheckboxDataItem,
    IColorTypes,
} from '@/types'

import { computed, onMounted, ref, watch } from 'vue'

import { useRoute, useRouter } from 'vue-router'

import { useVuelidate } from '@vuelidate/core'
import { helpers, minLength, /*maxLength,*/ minValue, required, numeric, integer } from '@vuelidate/validators'

import { useAssemblyStore } from '@/stores/AssemblyStore.ts'

import { ASSEMBLY_MODEL_MANUFACTURE_GROUP_DRAFT } from '@/app/constants/assembly.ts'

import { checkCRUD } from '@/app/helpers/helpers_checks.ts'

import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'
import AppInputTextAreaSimpleTS from '@/components/ui/inputs/AppInputTextAreaSimpleTS.vue'
import AppInputTextTS from '@/components/ui/inputs/AppInputTextTS.vue'
import AppInputNumberSimpleTS from '@/components/ui/inputs/AppInputNumberSimpleTS.vue'
import AppCallout from '@/components/ui/callouts/AppCallout.vue'
import AppModalAsyncMultiline from '@/components/ui/modals/AppModalAsyncMultiline.vue'
import AppCheckboxTSReactive from '@/components/ui/checkboxes/AppCheckboxTSReactive.vue'

// import AppModalAsyncSelectTS from '@/components/ui/modals/AppModalAsyncSelectTS.vue'

const assemblyStore = useAssemblyStore()

const route  = useRoute()
const router = useRouter()

const updateKey = 0

const FIELD_WIDTH_LABEL     = 'w-[300px]'
const FIELD_WIDTH_CHECK_BOX = 'w-[610px]'

const isLoading     = ref(false)
const isFormCorrect = ref(false)
const editMode      = ref(false)         // определяем режим работы формы (редактирование или создание)
let paramId: number = 0

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

// __ Подготавливаем переменные
const modelManufactureGroup = ref<IAssemblyModelManufactureGroup>(ASSEMBLY_MODEL_MANUFACTURE_GROUP_DRAFT)

// __ Подгружаем данные, если мы в режиме редактирования
const loadEntity = async (paramId: number) => {
    if (editMode.value) {
        modelManufactureGroup.value = await assemblyStore.getModelManufactureGroupById(paramId) as IAssemblyModelManufactureGroup
    } else {
        modelManufactureGroup.value = JSON.parse(JSON.stringify(ASSEMBLY_MODEL_MANUFACTURE_GROUP_DRAFT))
    }
}

// __ Определяем объекты валидации
const name        = ref('')
const groupNumber = ref(0)
const active      = ref(true)
const description = ref('')

// __ Заполняем объекты валидации
const fillData = () => {
    name.value        = modelManufactureGroup.value.name
    groupNumber.value = modelManufactureGroup.value.group_number
    active.value      = modelManufactureGroup.value.active
    description.value = modelManufactureGroup.value.description ?? ''
}

// __ Определяем объект валидации
const verify = {
    name,
    groupNumber,
    description,
}

// __ Определяем правила валидации
const MIN_NAME_LENGTH  = 3
const REQUIRED_MESSAGE = 'Поле обязательно для заполнения'
// const CODE_1C_LENGTH   = 9

const rules = {
    // id: {
    //     // $lazy: true,
    //     $autoDirty: true,
    //     // numeric: helpers.withMessage(`Поле должно содержать только цифры`, numeric),
    //     minValue: helpers.withMessage(`Поле должно быть больше или равно -1`, minValue(-1)),
    //     integer: helpers.withMessage(`Поле должно быть целочисленным`, integer),
    //     required: helpers.withMessage(REQUIRED_MESSAGE, required),
    // },
    //
    // code_1c    : {
    //     $autoDirty: true,
    //     required  : helpers.withMessage(REQUIRED_MESSAGE, required),
    //     minLength : helpers.withMessage(
    //         `Длина кода из 1С - ${CODE_1C_LENGTH} символов`,
    //         minLength(CODE_1C_LENGTH),
    //     ),
    //     maxLength : helpers.withMessage(
    //         `Длина кода из 1С - ${CODE_1C_LENGTH} символов`,
    //         maxLength(CODE_1C_LENGTH),
    //     ),
    // },
    name       : {
        $autoDirty: true,
        required  : helpers.withMessage(REQUIRED_MESSAGE, required),
        minLength : helpers.withMessage(
            `Минимальная длина названия - ${MIN_NAME_LENGTH} символов`,
            minLength(MIN_NAME_LENGTH),
        ),
    },
    groupNumber: {
        $autoDirty: true,
        minValue  : helpers.withMessage(`Поле должно быть больше или равно 1`, minValue(1)),
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
        modelManufactureGroup.value.active = data.id === 1
        active.value                       = modelManufactureGroup.value.active
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

    modelManufactureGroup.value.name         = name.value
    modelManufactureGroup.value.group_number = groupNumber.value
    modelManufactureGroup.value.active       = active.value
    modelManufactureGroup.value.description  = description.value

    console.log('modelManufactureGroup.value', modelManufactureGroup.value)

    let result

    if (!editMode.value) {
        result = await assemblyStore.createModelManufactureGroup(modelManufactureGroup.value)
    } else {
        result = await assemblyStore.updateModelManufactureGroup(modelManufactureGroup.value)
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
    () => name,
    () => groupNumber,
    () => description,
], async () => {
    isFormCorrect.value = await v$.value.$validate() // валидируем всю форму
}, { deep: true, immediate: true })

// __ Запускаем сразу валидацию формы
onMounted(async () => {
    // warn: Порядок важен!
    isLoading.value = true

    modelManufactureGroup.value = JSON.parse(JSON.stringify(ASSEMBLY_MODEL_MANUFACTURE_GROUP_DRAFT))

    await router.isReady().then(() => {
        paramId        = route.params.id as unknown as number
        editMode.value = route.meta.mode === 'edit' // определяем режим работы формы (редактирование или создание)
    })

    await loadEntity(paramId)

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
