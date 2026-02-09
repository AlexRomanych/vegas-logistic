<template>
    <div v-if="!isLoading" :key="updateKey" class="m-2 p-2 w-max-fit">

        <form @submit.prevent="formSubmit">

            <div class="border-2 rounded-lg border-slate-400 p-2 size-fit">

                <!-- __ Фамилия -->
                <AppInputTextTS
                    id="surname"
                    v-model:textValue.trim="v$.surname.$model as unknown as string"
                    :errors="v$.surname.$errors"
                    label="Фамилия"
                    mode="text"
                    placeholder="Введите фамилию..."
                />

                <!-- __ Имя -->
                <AppInputTextTS
                    id="name"
                    v-model:textValue.trim="v$.name.$model as unknown as string"
                    :errors="v$.name.$errors"
                    label="Имя"
                    mode="text"
                    placeholder="Введите имя..."
                />

                <!-- __ Отчество -->
                <AppInputTextTS
                    id="patronymic"
                    v-model:textValue.trim="v$.patronymic.$model as unknown as string"
                    :errors="v$.patronymic.$errors"
                    label="Отчество"
                    mode="text"
                    placeholder="Введите отчество..."
                />

                <!-- __ Актуальность -->
                <div class="mt-5"></div>
                <AppCheckboxTS
                    id="active"
                    :checkboxData="activeCheckboxData"
                    dir="horizontal"
                    inputType="radio"
                    legend="Актуальность записи"
                    type="secondary"
                    width="w-[500px]"
                    @checked="activeCheckedHandler"
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

                <!-- __ Описание клиента -->
                <AppInputTextAreaSimpleTS
                    id="description"
                    v-model:text-value.trim="v$.description.$model as unknown as string"
                    :value="v$.description.$model"
                    label="Описание клиента"
                    placeholder="Заполните описание..."
                />

                <!-- __ К списку -->
                <div class="m-3 mt-5 flex justify-between">
                    <router-link :to="{ name: 'workers' }">
                        <AppInputButton
                            id="returnButton"
                            func="button"
                            title="К списку сотрудников"
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

    <AppCallout
        :show="calloutShow"
        :text="calloutMessage"
        :type="calloutType"
        @toggleShow="calloutHandler"
    />

</template>

<script lang="ts" setup>
import type { ICheckboxData, ICheckboxDataItem, IWorker } from '@/types'

import { onMounted, ref, watch } from 'vue'

import { useRoute, useRouter } from 'vue-router'

import { useVuelidate } from '@vuelidate/core'
import { required, minLength, helpers, /*minValue, integer*/ } from '@vuelidate/validators'

import { useWorkersStore } from '@/stores/WorkersStore.ts'

import { WORKER_DRAFT } from '@/app/constants/workers.ts'

import { checkCRUD } from '@/app/helpers/helpers_checks.ts'

import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'
import AppCheckboxTS from '@/components/ui/checkboxes/AppCheckboxTS.vue'
import AppInputTextAreaSimpleTS from '@/components/ui/inputs/AppInputTextAreaSimpleTS.vue'
import AppInputTextTS from '@/components/ui/inputs/AppInputTextTS.vue'
import AppCallout from '@/components/ui/callouts/AppCallout.vue'
// import AppInputNumberSimpleTS from '@/components/ui/inputs/AppInputNumberSimpleTS.vue'


// __ Унифицируем типы
type IEntity = IWorker
const ENTITY_DRAFT = WORKER_DRAFT


const workersStore = useWorkersStore()

const route  = useRoute()
const router = useRouter()                 // Определяем роутер

let updateKey = 0

const isLoading     = ref(false)
const isFormCorrect = ref(false)
const editMode      = ref(false)         // определяем режим работы формы (редактирование или создание)
let paramId: number = -1

const calloutShow    = ref(false)       // состояние окна
const confirmClick   = ref(false)       // определяем для вывода этого callout
const calloutMessage = ref('')          // определяем показываемое сообщение
const calloutType    = ref('danger')    // определяем тип callout
const calloutHandler = () => setInterval(() => (confirmClick.value = false), 5000)


// __ Подготавливаем переменные
const entity = ref<IEntity>(ENTITY_DRAFT)
let activeCheckboxData: ICheckboxData         // выбор активности
// let calcModeCheckboxData: ICheckboxData       // выбор типа расчета

// __ Подгружаем данные по типу отхода, если мы в режиме редактирования
const loadEntity = async (paramId: number) => {
    if (editMode.value) {
        entity.value = await workersStore.getWorkerById(paramId) as IEntity // Получаем Операцию
    } else {
        entity.value = JSON.parse(JSON.stringify(ENTITY_DRAFT))
    }
}


// __ Определяем объекты валидации
// const id = ref(-1)
const surname     = ref('')
const name        = ref('')
const patronymic  = ref('')
const active      = ref(true)
const description = ref('')

// __ Заполняем объекты валидации
const fillData = () => {
    surname.value     = entity.value.surname
    name.value        = entity.value.name
    patronymic.value  = entity.value.patronymic ?? ''
    active.value      = entity.value.active
    description.value = entity.value.description ?? ''
}


// __ Определяем объект валидации
const verify = {
    // id,
    surname,
    name,
    patronymic,
    description,
}

// __ Определяем правила валидации
const MIN_NAME_LENGTH  = 2
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
    surname:    {
        $autoDirty: true,
        required:   helpers.withMessage(REQUIRED_MESSAGE, required),
        minLength:  helpers.withMessage(
            `Минимальная длина - ${MIN_NAME_LENGTH} символов`,
            minLength(MIN_NAME_LENGTH),
        ),
    },
    name:       {
        $autoDirty: true,
        required:   helpers.withMessage(REQUIRED_MESSAGE, required),
        minLength:  helpers.withMessage(
            `Минимальная длина - ${MIN_NAME_LENGTH} символов`,
            minLength(MIN_NAME_LENGTH),
        ),
    },
    patronymic: {
        $autoDirty: true,
        required:   helpers.withMessage(REQUIRED_MESSAGE, required),
        minLength:  helpers.withMessage(
            `Минимальная длина - ${MIN_NAME_LENGTH + 1} символов`,
            minLength(MIN_NAME_LENGTH + 1),
        ),
    },
    // time:        {
    //     $autoDirty: true,
    //     minValue:   helpers.withMessage(`Поле должно быть больше или равно 0`, minValue(0)),
    //     integer:    helpers.withMessage(`Поле должно быть целочисленным`, integer),
    //     required:   helpers.withMessage(REQUIRED_MESSAGE, required),
    //     // $lazy: true,
    //     // numeric: helpers.withMessage(`Поле должно содержать только цифры`, numeric),
    // },
    description: {},
}

// __ Оборачиваем в объект
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
        entity.value.active = data.id === 1
        active.value        = entity.value.active
    }
}

// __ Отправка формы
const formSubmit = async () => {

    isFormCorrect.value = await v$.value.$validate() // валидируем всю форму
    if (!isFormCorrect.value) return // это показатель ошибки

    entity.value.surname     = surname.value
    entity.value.name        = name.value
    entity.value.patronymic  = patronymic.value
    entity.value.active      = active.value
    entity.value.description = description.value

    console.log('entity: ', entity.value)

    let result

    if (!editMode.value) {
        result = await workersStore.createWorker(entity.value)
    } else {
        result = await workersStore.updateWorker(entity.value)
    }

    if (checkCRUD(result.data)) {
        calloutMessage.value = 'Данные успешно сохранены'
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
    () => surname,
    () => name,
    () => patronymic,
    () => description,
], async () => {
    isFormCorrect.value = await v$.value.$validate() // валидируем всю форму
}, { deep: true, immediate: true })

// __ Запускаем сразу валидацию формы
onMounted(async () => {
    // warn: Порядок важен!
    isLoading.value = true

    entity.value = JSON.parse(JSON.stringify(ENTITY_DRAFT))

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
