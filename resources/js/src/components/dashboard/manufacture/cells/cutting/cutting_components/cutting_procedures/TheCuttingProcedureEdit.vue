<template>
    <div v-if="!isLoading" class="m-2 p-2 w-max-fit">

        <form @submit.prevent="formSubmit">

            <div class="border-2 rounded-lg border-slate-400 p-2 size-fit">

                <!--&lt;!&ndash; __ id клиента (доступен только в режиме создания) &ndash;&gt;-->
                <!--<AppInputNumberSimpleTS-->
                <!--    id="id"-->
                <!--    v-model:input-number.number="v$.id.$model as unknown as number"-->
                <!--    :disabled="editMode"-->
                <!--    :errors="v$.id.$errors"-->
                <!--    label="ID клиента (ID = -1 автоматическое присвоение)"-->
                <!--    mode="text"-->
                <!--    placeholder="Введите ID клиента..."-->
                <!--    width="w-[500px]"-->
                <!--/>-->

                <!-- __ Название Процедуры -->
                <AppInputTextTS
                    id="name"
                    v-model:textValue.trim="v$.name.$model as unknown as string"
                    :errors="v$.name.$errors"
                    :width="DEFAULT_WIDTH"
                    label="Название Процедуры"
                    mode="text"
                    placeholder="Укажите название процедуры..."
                />

                <!-- __ Object_name -->
                <AppInputTextTS
                    id="object-name"
                    v-model:textValue.trim="v$.objectName.$model as unknown as string"
                    :errors="v$.objectName.$errors"
                    :width="DEFAULT_WIDTH"
                    disabled
                    label="Объект Процедуры"
                    mode="text"
                    placeholder="Укажите объект процедуры..."
                />

                <!-- __ Актуальность -->
                <div class="mt-5"></div>
                <AppCheckboxTS
                    id="active"
                    :checkboxData="activeCheckboxData"
                    :width="DEFAULT_WIDTH"
                    dir="horizontal"
                    inputType="radio"
                    legend="Актуальность записи"
                    type="secondary"
                    @checked="activeCheckedHandler"
                />

                <!-- __ Описание процедуры -->
                <AppInputTextAreaSimpleTS
                    id="description"
                    v-model:text-value.trim="v$.description.$model as unknown as string"
                    :value="v$.description.$model"
                    :width="DEFAULT_WIDTH"
                    label="Описание процедуры"
                    placeholder="Заполните описание..."
                />

                <!-- __ Сама процедура -->
                <AppInputTextAreaSimpleTS
                    id="machine"
                    v-model:textValue.trim="v$.text.$model as unknown as string"
                    :bold="false"
                    :errors="v$.text.$errors"
                    :maxlength="5000"
                    :read="false"
                    :rows="15"
                    :width="DEFAULT_WIDTH"
                    height="min-h-[300px]"
                    label="Процедура Раскроя"
                    mode="text"
                    placeholder="Текст процедуры..."
                    text-size="normal"
                />

                <!--&lt;!&ndash; __ Время операции &ndash;&gt;-->
                <!--<AppInputNumberSimpleTS-->
                <!--    id="time"-->
                <!--    v-model:input-number.number="v$.time.$model as unknown as number"-->
                <!--    :errors="v$.time.$errors"-->
                <!--    label="Время операции"-->
                <!--    mode="text"-->
                <!--    placeholder="Время операции..."-->
                <!--    width="w-[500px]"-->
                <!--/>-->


                <!-- __ К списку -->
                <div class="m-3 mt-5 flex justify-between">
                    <router-link :to="{ name: 'manufacture.cell.cutting.procedures' }">
                        <AppInputButton
                            id="returnButton"
                            func="button"
                            title="К списку процедур"
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
                        width="w-[300px]"
                    />
                </div>

            </div>

        </form>

    </div>

    <AppCallout
        :show="calloutShow"
        :text="calloutMessage"
        :type="calloutType"
        @toggleShow="calloutHandler"
    />

</template>

<script lang="ts" setup>
import type { ICheckboxData, ICheckboxDataItem, ICuttingProcedure } from '@/types'

import { onMounted, ref, watch, } from 'vue'

import { useRoute, useRouter } from 'vue-router'

import { useVuelidate } from '@vuelidate/core'
import { required, minLength, helpers, /*minValue,*/ /*integer*/ } from '@vuelidate/validators'

import { useCuttingStore } from '@/stores/CuttingStore'

import { CUTTING_PROCEDURE_DRAFT, } from '@/app/constants/cutting.ts'

import { checkCRUD } from '@/app/helpers/helpers_checks.ts'

import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'
import AppCheckboxTS from '@/components/ui/checkboxes/AppCheckboxTS.vue'
import AppInputTextAreaSimpleTS from '@/components/ui/inputs/AppInputTextAreaSimpleTS.vue'
import AppInputTextTS from '@/components/ui/inputs/AppInputTextTS.vue'
import AppCallout from '@/components/ui/callouts/AppCallout.vue'
// import AppInputNumberSimpleTS from '@/components/ui/inputs/AppInputNumberSimpleTS.vue'


const cuttingStore = useCuttingStore()

const route  = useRoute()
const router = useRouter()

// const updateKey = 0
const DEFAULT_WIDTH = 'w-[1000px]'

const isLoading     = ref(false)
const isFormCorrect = ref(false)
const editMode      = ref(false)        // определяем режим работы формы (редактирование или создание)
let paramId: number = -1

const calloutShow    = ref(false)       // состояние окна
const confirmClick   = ref(false)       // определяем для вывода этого callout
const calloutMessage = ref('')          // определяем показываемое сообщение
const calloutType    = ref('danger')    // определяем тип callout
const calloutHandler = () => setInterval(() => (confirmClick.value = false), 5000)


// __ Подготавливаем переменные
const procedure = ref<ICuttingProcedure>(CUTTING_PROCEDURE_DRAFT)
let activeCheckboxData: ICheckboxData         // выбор активности

// __ Подгружаем данные по операции, если мы в режиме редактирования
const loadEntity = async (paramId: number) => {
    if (editMode.value) {
        procedure.value = await cuttingStore.getCuttingProcedure(paramId) as ICuttingProcedure // Получаем Процедуру
    } else {
        procedure.value = JSON.parse(JSON.stringify(CUTTING_PROCEDURE_DRAFT))
    }
}


// __ Определяем объекты валидации
// const id = ref(-1)
const name        = ref('')
const objectName  = ref('')
const active      = ref(true)
const description = ref('')
const text        = ref('')


// __ Заполняем объекты валидации
const fillData = () => {
    name.value        = procedure.value.name
    text.value        = procedure.value.text ?? ''
    objectName.value  = procedure.value.object_name ?? 'Крышка'
    description.value = procedure.value.description ?? ''
}


// __ Определяем объект валидации
const verify = {
    // id,
    name,
    text,
    objectName,
    description,
}

// const verify = {
//     // id,
//     name,
//     text,
//     objectName,
//     description,
// }

// __ Определяем правила валидации
const MIN_NAME_LENGTH   = 10
const MIN_OBJECT_LENGTH = 5
const REQUIRED_MESSAGE  = 'Поле обязательно для заполнения'

const rules = {
    // id: {
    //     // $lazy: true,
    //     $autoDirty: true,
    //     // numeric: helpers.withMessage(`Поле должно содержать только цифры`, numeric),
    //     minValue: helpers.withMessage(`Поле должно быть больше или равно -1`, minValue(-1)),
    //     integer: helpers.withMessage(`Поле должно быть целочисленным`, integer),
    //     required: helpers.withMessage(REQUIRED_MESSAGE, required),
    // },
    name       : {
        $autoDirty: true,
        required  : helpers.withMessage(REQUIRED_MESSAGE, required),
        minLength : helpers.withMessage(
            `Минимальная длина названия - ${MIN_NAME_LENGTH} символов`,
            minLength(MIN_NAME_LENGTH),
        ),
    },
    objectName : {
        $autoDirty: true,
        required  : helpers.withMessage(REQUIRED_MESSAGE, required),
        minLength : helpers.withMessage(
            `Минимальная длина названия - ${MIN_OBJECT_LENGTH} символов`,
            minLength(MIN_OBJECT_LENGTH),
        ),
    },
    text       : {},
    description: {},
}

// __ Оборачиваем в объект
// const v$ = useVuelidate(rules, procedure)
const v$ = useVuelidate(rules, verify)

// __ Заполняем селекты данными
const fillSelects = () => {
    activeCheckboxData = {
        name: 'activity',
        data: [
            { id: 1, name: 'Активная', checked: active.value },
            { id: 2, name: 'Архив', checked: !active.value },
        ],
    }

}


// __ Обработчик чекбокса на active
const activeCheckedHandler = (data: ICheckboxDataItem | ICheckboxDataItem[]) => {
    if (!Array.isArray(data)) {
        procedure.value.active = data.id === 1
        active.value           = procedure.value.active
    }
}


// __ Отправка формы
const formSubmit = async () => {

    isFormCorrect.value = await v$.value.$validate() // валидируем всю форму
    if (!isFormCorrect.value) return // это показатель ошибки

    procedure.value.name        = name.value
    procedure.value.active      = active.value
    procedure.value.text        = text.value
    procedure.value.object_name = objectName.value
    procedure.value.description = description.value

    console.log('procedure.value', procedure.value)

    let result

    if (!editMode.value) {
        result = await cuttingStore.createCuttingProcedure(procedure.value)
    } else {
        result = await cuttingStore.updateCuttingProcedure(procedure.value)
    }

    if (checkCRUD(result.data)) {
        calloutMessage.value = result.payload
        calloutType.value    = 'success'
    } else {
        calloutMessage.value = result.error
        calloutType.value    = 'danger'
    }

    confirmClick.value = true   // подтверждаем клик
    calloutShow.value  = true    // показываем callout
    calloutHandler()            // запускаем таймер на скрытие callout
}

watch([
    () => name.value,
    () => text.value,
    () => description.value,
    () => objectName.value
], async () => {
    isFormCorrect.value = await v$.value.$validate() // валидируем всю форму
}, { deep: true, immediate: true })

// __ Запускаем сразу валидацию формы
onMounted(async () => {
    // warn: Порядок важен!
    isLoading.value = true

    procedure.value = JSON.parse(JSON.stringify(CUTTING_PROCEDURE_DRAFT))

    await router.isReady().then(() => {
        paramId        = route.params.id as unknown as number
        editMode.value = route.meta.mode === 'edit' // определяем режим работы формы (редактирование или создание)
    })
    await loadEntity(paramId)

    fillData()
    fillSelects()

    v$.value.$touch()
    isFormCorrect.value = await v$.value.$validate() // валидируем всю форму
    isLoading.value     = false

    // console.log('editMode.value: ', editMode.value)
})

</script>

<style scoped>

</style>
