<template>
    <div v-if="!isLoading" class="m-2 p-2 w-max-fit">

        <form @submit.prevent="formSubmit">

            <div class="border-2 rounded-lg border-slate-400 p-2 size-fit">

                <!-- __ Клиент -->
                <AppLabelTS
                    :text="clientRenderName"
                    :width="DEFAULT_WIDTH"
                    height="h-[50px]"
                    type="indigo"
                    @click="selectClient"
                />

                <!-- __ Номер Заявки -->
                <AppInputTextTS
                    id="name"
                    v-model:textValue.trim="v$.orderNo.$model as unknown as string"
                    :errors="v$.orderNo.$errors"
                    :width="DEFAULT_WIDTH"
                    label="Номер Заявки"
                    mode="text"
                    placeholder="Номер Заявки..."
                />

                <!-- __ Тип изделий-->
                <div class="mt-5"></div>
                <AppCheckboxTS
                    id="calc-mode"
                    :checkboxData="elementTypeCheckboxData"
                    :width="DEFAULT_WIDTH"
                    dir="horizontal"
                    inputType="radio"
                    legend="Тип изделий"
                    type="secondary"
                    @checked="elementTypesCheckedHandler"
                />

                <!-- __ Назначение (функция) -->
                <div class="mt-5"></div>
                <AppCheckboxTS
                    id="calc-mode"
                    :checkboxData="functionTypeCheckboxData"
                    :width="DEFAULT_WIDTH"
                    dir="horizontal"
                    inputType="radio"
                    legend="Назначение"
                    type="secondary"
                    @checked="functionTypesCheckedHandler"
                />

                <!-- __ Кол-во изделий -->
                <AppInputNumberSimpleTS
                    id="amount"
                    v-model:input-number.number="v$.amount.$model as unknown as number"
                    :errors="v$.amount.$errors"
                    :width="DEFAULT_WIDTH"
                    label="Количество изделий"
                    mode="text"
                    placeholder="Количество изделий..."
                />

                <div class="min-h-[15px]"></div>

                <!-- __ Дата загрузки на складе -->
                <AppInputDateTS
                    id="start"
                    v-model="loadDate"
                    :width="DEFAULT_WIDTH"
                    class="mr-1 ml-0.5"
                    label="Дата загрузки на складе:"
                    type="light"
                />

                <!-- __ К списку -->
                <div class="m-3 mt-5 flex justify-between">
                    <router-link :to="{ name: 'orders' }">
                        <AppInputButton
                            id="returnButton"
                            func="button"
                            title="К списку заявок"
                            width="w-[230px]"
                        />
                    </router-link>


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

    <!-- __ Выпадающий список-->
    <AppModalAsyncSelectTSFuncUpgraded
        ref="appModalAsyncSelectTS"
        v-model="selectedClientId"
        :func="(client) => `${client!.short_name}`"
        :items="globalClients"
        title="Выберите клиента"
        width="w-[600px]"/>


    <AppCallout
        :show="calloutShow"
        :text="calloutMessage"
        :type="calloutType"
        @toggleShow="calloutHandler"
    />

</template>

<script lang="ts" setup>
import type {
    ICheckboxData,
    ICheckboxDataItem,
    IElementTypeUnion,
} from '@/types'

import { onMounted, ref, watch, computed } from 'vue'
import { storeToRefs } from 'pinia'

import { useVuelidate } from '@vuelidate/core'
import { required, /*minLength,*/ helpers, minValue, integer, numeric } from '@vuelidate/validators'

import { useOrdersStore } from '@/stores/OrdersStore'
import { useClientsStore } from '@/stores/ClientsStore'

import { ACCESSORIES_TYPE, MATTRESSES_TYPE } from '@/app/constants/orders.ts'

import { checkCRUD } from '@/app/helpers/helpers_checks.ts'

import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'
import AppCheckboxTS from '@/components/ui/checkboxes/AppCheckboxTS.vue'
import AppInputNumberSimpleTS from '@/components/ui/inputs/AppInputNumberSimpleTS.vue'
import AppCallout from '@/components/ui/callouts/AppCallout.vue'
import AppModalAsyncSelectTSFuncUpgraded from '@/components/ui/modals/AppModalAsyncSelectTSFuncUpgraded.vue'
import AppLabelTS from '@/components/ui/labels/AppLabelTS.vue'
import AppInputTextTS from '@/components/ui/inputs/AppInputTextTS.vue'
import AppInputDateTS from '@/components/ui/inputs/AppInputDateTS.vue'
import {/* formatDateTime,*/ getISOFromLocaleDate } from '@/app/helpers/helpers_date'

type IOrderFunctionType = 'raw' | 'tasks' // __ Сырье или СЗ

const DEFAULT_WIDTH = 'w-[500px]'

// __ Тип для модального окна выбора Клиента
const selectedClientId      = ref<number | null>(0)
const appModalAsyncSelectTS = ref<any>(null)

const clientsStore = useClientsStore()
const ordersStore  = useOrdersStore()

const { globalClients } = storeToRefs(clientsStore)

// __ Определяем объекты валидации
const orderNo      = ref('')
const amount       = ref(0)
const loadDate     = ref<Date>(new Date())
const elementType  = ref<IElementTypeUnion>(MATTRESSES_TYPE)
const functionType = ref<IOrderFunctionType>('raw')


const clientRenderName = computed(() => {
    const client = globalClients.value.find(item => item.id === selectedClientId.value)
    return client ? `Клиент: ${client.short_name}` : ''
})

const isLoading     = ref(false)
const isFormCorrect = ref(false)

const calloutShow    = ref(false)      // состояние окна
const confirmClick   = ref(false)     // определяем для вывода этого callout
const calloutMessage = ref('')      // определяем показываемое сообщение
const calloutType    = ref('danger')   // определяем тип callout
const calloutHandler = () => setInterval(() => (confirmClick.value = false), 5000)


// __ Подготавливаем переменные
let elementTypeCheckboxData: ICheckboxData         // __ выбор типа изделий
let functionTypeCheckboxData: ICheckboxData        // __ выбор функцтт Заявки: для расчеты сырья или для создания СЗ


// __ Определяем объекты валидации
const name        = ref('')
const machine     = ref('')
const time        = ref(0)
const description = ref('')
// const active      = ref(true)

// __ Определяем объект валидации
const verify = {
    amount,
    orderNo,
}

// __ Определяем правила валидации
const REQUIRED_MESSAGE = 'Поле обязательно для заполнения'

const rules = {
    amount : {
        $autoDirty: true,
        minValue  : helpers.withMessage(`Поле должно быть больше 0`, minValue(1)),
        integer   : helpers.withMessage(`Поле должно быть целочисленным`, integer),
        required  : helpers.withMessage(REQUIRED_MESSAGE, required),
        numeric   : helpers.withMessage(`Поле должно содержать только цифры`, numeric),
    },
    orderNo: {
        $autoDirty: true,
        required  : helpers.withMessage(REQUIRED_MESSAGE, required),
        numeric   : helpers.withMessage(`Поле должно содержать только цифры`, numeric),
    },
}

// __ Оборачиваем в объект
const v$ = useVuelidate(rules, verify)

// __ Заполняем селекты данными
const fillSelects = () => {
    elementTypeCheckboxData = {
        name: 'element-type',
        data: [
            { id: 1, name: 'Матрасы', checked: elementType.value === MATTRESSES_TYPE },
            { id: 2, name: 'Аксессуары', checked: elementType.value === ACCESSORIES_TYPE },
        ],
    }

    functionTypeCheckboxData = {
        name: 'function-type',
        data: [
            { id: 1, name: 'Прогнозная (для Сырья)', checked: functionType.value === 'raw' },
            { id: 2, name: 'Техническая (для СЗ)', checked: functionType.value === 'tasks' },
        ],
    }
}

// __ Обработчик чекбокса на Типе изделий
const elementTypesCheckedHandler = (data: ICheckboxDataItem | ICheckboxDataItem[]) => {
    if (!Array.isArray(data)) {
        elementType.value = data.id === 1 ? MATTRESSES_TYPE : ACCESSORIES_TYPE
    }
}

// __ Обработчик чекбокса на Типе функции
const functionTypesCheckedHandler = (data: ICheckboxDataItem | ICheckboxDataItem[]) => {
    if (!Array.isArray(data)) {
        functionType.value = data.id === 1 ? 'raw' : 'tasks'
    }
}

// __ Выбираем Клиента
const selectClient = async () => {
    await appModalAsyncSelectTS.value!.show(selectedClientId.value)
}

// __ Отправка формы
const formSubmit = async () => {

    isFormCorrect.value = await v$.value.$validate() // валидируем всю форму
    if (!isFormCorrect.value) return // это показатель ошибки

    const result = await ordersStore.addOrdersAverage(
        selectedClientId.value,
        orderNo.value,
        amount.value,
        elementType.value,
        getISOFromLocaleDate(loadDate.value),
        functionType.value,
    )

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
    () => name,
    () => machine,
    () => description,
    () => time,
], async () => {
    isFormCorrect.value = await v$.value.$validate() // валидируем всю форму
}, { deep: true, immediate: true })


watch(loadDate, (newDate) => console.log(getISOFromLocaleDate(newDate)))

// __ Запускаем сразу валидацию формы
onMounted(async () => {
    // warn: Порядок важен!
    isLoading.value = true

    if (globalClients.value.length === 0) {
        await clientsStore.getClients(true)
        const zeroClientIndex = globalClients.value.findIndex(item => item.id === 0)
        if (zeroClientIndex !== -1) {
            selectedClientId.value = globalClients.value[zeroClientIndex].id
        } else {
            selectedClientId.value = globalClients.value?.[0].id
        }
    }

    fillSelects()

    v$.value.$touch()
    isFormCorrect.value = await v$.value.$validate() // валидируем всю форму
    isLoading.value     = false
})

</script>

<style scoped>

</style>
