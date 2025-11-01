<template>
    <div v-if="!isLoading" class="m-2 p-2 w-max-fit" :key="updateKey" >

        <form @submit.prevent="formSubmit">

            <div class="border-2 rounded-lg border-slate-400 p-2 size-fit">

                <!-- __ id клиента (доступен только в режиме создания) -->
                <AppInputNumberSimpleTS
                    id="id"
                    v-model:input-number.number="v$.id.$model as unknown as number"
                    :disabled="editMode"
                    :errors="v$.id.$errors"
                    label="ID клиента (ID = -1 автоматическое присвоение)"
                    mode="text"
                    placeholder="Введите ID клиента..."
                    width="w-[500px]"
                />

                <!-- __ Название клиента -->
                <AppInputTextTS
                    id="name"
                    v-model:textValue.trim="v$.name.$model as unknown as string"
                    :errors="v$.name.$errors"
                    label="Название клиента"
                    mode="text"
                    placeholder="Введите название клиента..."
                />

                <!-- __ Дополнительное название клиента -->
                <AppInputTextTS
                    id="add-name"
                    v-model:textValue.trim="v$.addName.$model as unknown as string"
                    :errors="v$.addName.$errors"
                    label="Дополнительное название клиента"
                    mode="text"
                    placeholder="Введите дополнительное название клиента..."
                />

                <!-- __ Сокращенное название клиента -->
                <AppInputTextTS
                    id="short-name"
                    v-model:textValue.trim="v$.shortName.$model as unknown as string"
                    :errors="v$.shortName.$errors"
                    label="Сокращенное название клиента"
                    mode="text"
                    placeholder="Введите сокращенное название клиента..."
                />

                <!-- __ Регион -->
                <div class="mt-5"></div>
                <AppCheckboxTS
                    id="region"
                    :checkboxData="regionCheckboxData"
                    dir="horizontal"
                    inputType="radio"
                    legend="Регион"
                    type="secondary"
                    width="w-[500px]"
                    @checked="regionCheckedHandler"
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

                <!-- __ Описание клиента -->
                <AppInputTextAreaSimpleTS
                    id="description"
                    v-model:text-value.trim="v$.description.$model as unknown as string"
                    :value="v$.description.$model"
                    label="Описание клиента"
                    placeholder="Заполните описание..."
                />

                <!-- __ Комментарий к клиенту -->
                <AppInputTextAreaSimpleTS
                    id="comment"
                    v-model:text-value.trim="v$.comment.$model as unknown as string"
                    :value="v$.comment.$model"
                    label="Комментарий к клиенту"
                    placeholder="Заполните комментарий..."
                />


                <div class="m-3 mt-5 flex justify-between">
                    <router-link :to="{ name: 'clients' }">
                        <AppInputButton
                            id="returnButton"
                            func="button"
                            title="К списку клиентов"
                            width="w-[230px]"
                        />
                    </router-link>

                    <!--                    <AppInputButton-->
                    <!--                        id="resetButton"-->
                    <!--                        func="reset"-->
                    <!--                        title="Сброс"-->
                    <!--                        type="danger"-->
                    <!--                        width="w-[150px]"-->
                    <!--                    />-->

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
import type { ICheckboxData, ICheckboxDataItem, IClient, IClientRegion } from '@/types'

import { onMounted, onUnmounted, ref, watch } from 'vue'

import { onBeforeRouteLeave, onBeforeRouteUpdate, useRoute, useRouter } from 'vue-router'

import { useVuelidate } from '@vuelidate/core'
import { required, minLength, helpers, numeric, minValue, integer } from '@vuelidate/validators'

import { useClientsStore } from '@/stores/ClientsStore'

import { CLIENT_DRAFT } from '@/app/constants/clients.ts'

import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'
import AppCheckboxTS from '@/components/ui/checkboxes/AppCheckboxTS.vue'
import AppInputTextAreaSimpleTS from '@/components/ui/inputs/AppInputTextAreaSimpleTS.vue'
import AppInputTextTS from '@/components/ui/inputs/AppInputTextTS.vue'
import AppInputNumberSimpleTS from '@/components/ui/inputs/AppInputNumberSimpleTS.vue'
import { checkCRUD } from '@/app/helpers/helpers_checks.ts'
import { cloneDeep, deepCopy } from '@/app/helpers/helpers_lib.ts'
import AppCallout from '@/components/ui/callouts/AppCallout.vue'


const clientsStore = useClientsStore()

const route = useRoute()
const router = useRouter()                 // Определяем роутер

let updateKey = 0

const isLoading = ref(false)
const isFormCorrect = ref(false)
const editMode = ref(false)         // определяем режим работы формы (редактирование или создание)
let paramId: number = -1

const calloutShow = ref(false) // состояние окна
const confirmClick = ref(false) // определяем для вывода этого callout
const calloutMessage = ref('') // определяем показываемое сообщение
const calloutType = ref('danger') // определяем тип callout
const calloutHandler = () => setInterval(() => (confirmClick.value = false), 500)


// __ Подготавливаем переменные
const client = ref<IClient>(CLIENT_DRAFT)
let activeCheckboxData: ICheckboxData         // выбор активности
let regionCheckboxData: ICheckboxData         // выбор региона


// __ Подгружаем данные по типу отхода, если мы в режиме редактирования
const loadEntity = async (paramId: number) => {
    if (paramId < 0) return CLIENT_DRAFT
    if (editMode.value) {
        client.value = await clientsStore.getClient(paramId) as IClient // Получаем клиента
    }
}


// __ Определяем объекты валидации
const id = ref(-1)
const name = ref('')
const addName = ref('')
const shortName = ref('')
const active = ref(true)
const region = ref<IClientRegion>('east')
const description = ref('')
const comment = ref('')

// __ Заполняем объекты валидации
const fillData = () => {
    id.value = client.value.id
    name.value = client.value.name
    addName.value = client.value.add_name
    shortName.value = client.value.short_name
    active.value = client.value.active
    region.value = client.value.region ?? 'east'
    description.value = client.value.description ?? ''
    comment.value = client.value.comment ?? ''
}


// __ Определяем объект валидации
const verify = {
    id,
    name,
    addName,
    shortName,
    description,
    comment,
}

// __ Определяем правила валидации
const MIN_NAME_LENGTH = 10
const MIN_ADD_NAME_LENGTH = 3
const MIN_SHORT_NAME_LENGTH = 3
const REQUIRED_MESSAGE = 'Поле обязательно для заполнения'
// const MIN_VOLUME_LENGTH = 0.1

const rules = {
    id: {
        // $lazy: true,
        $autoDirty: true,
        // numeric: helpers.withMessage(`Поле должно содержать только цифры`, numeric),
        minValue: helpers.withMessage(`Поле должно быть больше или равно -1`, minValue(-1)),
        integer: helpers.withMessage(`Поле должно быть целочисленным`, integer),
        required: helpers.withMessage(REQUIRED_MESSAGE, required),
    },
    name: {
        // $lazy: true,
        $autoDirty: true,
        required: helpers.withMessage(REQUIRED_MESSAGE, required),
        minLength: helpers.withMessage(
            `Минимальная длина названия - ${MIN_NAME_LENGTH} символов`,
            minLength(MIN_NAME_LENGTH),
        ),
    },
    addName: {},
    shortName: {
        // $lazy: true,
        $autoDirty: true,
        required: helpers.withMessage(REQUIRED_MESSAGE, required),
        minLength: helpers.withMessage(
            `Минимальная длина названия - ${MIN_SHORT_NAME_LENGTH} символов`,
            minLength(MIN_SHORT_NAME_LENGTH),
        ),
    },
    description: {},
    comment: {},
}

// __ Оборачиваем в объект
const v$ = useVuelidate(rules, verify)

// __ Заполняем селекты данными
const fillSelects = () => {
    activeCheckboxData = {
        name: 'activity',
        data: [
            {id: 1, name: 'Активная', checked: active.value},
            {id: 2, name: 'Архив', checked: !active.value},
        ],
    }

    regionCheckboxData = {
        name: 'region',
        data: [
            {id: 1, name: 'Восток', checked: region.value === 'east'},
            {id: 2, name: 'Запад', checked: region.value === 'west'},
        ]
    }
}


// __ Обработчик чекбокса на active
const activeCheckedHandler = (data: ICheckboxDataItem | ICheckboxDataItem[]) => {
    if (!Array.isArray(data)) client.value.active = data.id === 1
}

// __ Обработчик чекбокса на region
const regionCheckedHandler = (data: ICheckboxDataItem | ICheckboxDataItem[]) => {
    if (!Array.isArray(data)) client.value.region = data.id === 1 ? 'east' : 'west'
}

// __ Отправка формы
const formSubmit = async () => {

    isFormCorrect.value = await v$.value.$validate() // валидируем всю форму
    if (!isFormCorrect.value) return // это показатель ошибки

    client.value.id = id.value
    client.value.name = name.value
    client.value.add_name = addName.value
    client.value.short_name = shortName.value
    client.value.description = description.value//  ?? null
    client.value.comment = comment.value//  ?? null

    // console.log('client.value', client.value)

    let result

    if (!editMode.value) {
        result = await clientsStore.createClient(client.value)
    } else {
        result = await clientsStore.updateClient(client.value)
    }


    if (checkCRUD(result.data)) {
        calloutMessage.value = result.payload
        calloutType.value = 'success'
    } else {
        calloutMessage.value = result.error
        calloutType.value = 'danger'
    }

    confirmClick.value = true   // подтверждаем клик
    calloutShow.value = true    // показываем callout
    calloutHandler()            // запускаем таймер на скрытие callout
}

watch([
    () => name,
    () => addName,
    () => shortName,
    () => description,
    () => comment], async () => {
    isFormCorrect.value = await v$.value.$validate() // валидируем всю форму
}, {deep: true, immediate: true})

// __ Запускаем сразу валидацию формы
onMounted(async () => {
    // warn: Порядок важен!
    isLoading.value = true

    client.value = deepCopy(CLIENT_DRAFT)
    client.value = cloneDeep(CLIENT_DRAFT)

    await router.isReady().then(() => {
        paramId = route.params.id as unknown as number
        editMode.value = route.meta.mode === 'edit' // определяем режим работы формы (редактирование или создание)
    })
    await loadEntity(paramId)

    fillData()
    fillSelects()

    v$.value.$touch()
    isFormCorrect.value = await v$.value.$validate() // валидируем всю форму
    isLoading.value = false

    console.log('editMode.value: ', editMode.value)
})



</script>

<style scoped>

</style>
