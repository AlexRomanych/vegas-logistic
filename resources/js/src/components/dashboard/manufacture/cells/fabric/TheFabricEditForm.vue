<template>

    <div v-if="!isLoading" class="m-2 p-2 w-max-fit">

        <form @submit.prevent="formSubmit">

            <div class="bg-slate-200 border-2 rounded-lg border-slate-400 p-2 size-fit">

                <div class="flex">

                    <!-- __ Код по 1С -->
                    <AppInputTextTS
                        id="code_1C"
                        v-model:textValue.trim="v$.code1C.$model as unknown as string"
                        :errors="v$.code1C.$errors"
                        label="Код по 1С"
                        placeholder="Код по 1С"
                        width="w-[100px]"

                    />

                    <!-- __ Название ПС -->
                    <AppInputTextTS
                        id="name"
                        v-model:textValue.trim="v$.name.$model as unknown as string"
                        :errors="v$.name.$errors"
                        label="Название ПС"
                        placeholder="Введите название ПС"
                        width="w-[364px]"
                    />
                </div>

                <div class="flex">

                    <!-- __ Средняя длина рулона ткани, м.п. -->
                    <AppInputNumberSimpleTS
                        id="average_textile_length"
                        v-model:inputNumber.number="v$.averageTextileLengthHand.$model as unknown as number"
                        :disabled="statistic"
                        :errors="v$.averageTextileLengthHand.$errors"
                        label="Средняя длина рулона ткани, м.п."
                        placeholder="Введите ср. длину рул. ткани"
                        step="0.0001"
                        width="w-[230px]"
                    />

                </div>

                <div class="flex">

                    <!-- __ Получать среднюю длину ткани из статистики -->
                    <AppLabelCheckBoxTS
                        :state="statistic"
                        align="center"
                        label="Ср. длина ткани из стат-ки (1 мес.)"
                        width="w-[230px]"
                        @label-click="statisticHandler"
                    />

                    <!--<AppCheckboxLine-->
                    <!--    id="get_average_textile_length"-->
                    <!--    :disabled="true"-->
                    <!--    :width="'w-[300px]'"-->
                    <!--    label="Получать среднюю длину ткани из статистики"-->
                    <!--/>-->

                    <!-- __ Средняя длина ткани по статистике -->
                    <AppInputNumberSimpleTS
                        id="buffer_rolls"
                        v-model:inputNumber.number="averageTextileLengthStatistic"
                        :disabled="true"
                        label="Ср. длина ткани по статистике (1 мес.)"
                        placeholder="Нет данных"
                        step="0.1"
                        width="w-[230px]"
                    />

                </div>


                <div class="flex">

                    <!-- __ Буфер, м.п. -->
                    <AppInputNumberSimpleTS
                        id="buffer_meters"
                        v-model:inputNumber.number="v$.bufferAmount.$model as unknown as number"
                        :errors="v$.bufferAmount.$errors"
                        label="Буфер, м.п."
                        placeholder="Введите остаток ПС (буфер)"
                        step="0.001"
                        width="w-[230px]"
                    />

                    <!-- __ Буфер, рул. -->
                    <AppInputNumberSimpleTS
                        id="buffer_rolls"
                        v-model:inputNumber.number="v$.bufferRolls.$model as unknown as number"
                        :disabled="true"
                        :errors="v$.bufferRolls.$errors"
                        label="Буфер, рул."
                        placeholder="Введите остаток ПС (буфер)"
                        step="0.1"
                        width="w-[230px]"
                    />
                </div>


                <div class="flex">

                    <!-- __ Мин. запас ПС, рул. -->
                    <AppInputNumberSimpleTS
                        id="min_buffer_rolls"
                        v-model:inputNumber.number="v$.minRolls.$model as unknown as number"
                        :errors="v$.minRolls.$errors"
                        label="Минимальный запас ПС, рул."
                        placeholder="Введите мин. запас"
                        step="1"
                        width="w-[230px]"
                    />

                    <!-- __ Макс. запас ПС, рул. -->
                    <AppInputNumberSimpleTS
                        id="max_buffer_rolls"
                        v-model:inputNumber.number="v$.maxRolls.$model as unknown as number"
                        :errors="v$.maxRolls.$errors"
                        label="Максимальный запас ПС, рул."
                        placeholder="Введите макс. запас"
                        step="1"
                        width="w-[230px]"
                    />

                </div>

                <div class="flex">

                    <!-- __ Оптимальная партия для запуска, м.п. (ОПЗ) -->
                    <AppInputNumberSimpleTS
                        id="optimal_party"
                        v-model:inputNumber.number="v$.optimalParty.$model as unknown as number"
                        :errors="v$.optimalParty.$errors"
                        label="Оптим. партия для запуска, м.п."
                        placeholder="Введите ОПЗ"
                        step="1"
                        width="w-[230px]"
                    />

                    <!-- __ Коэффициент перевода ткани в ПС -->
                    <AppInputNumberSimpleTS
                        id="rate"
                        v-model:inputNumber.number="v$.translateRate.$model as unknown as number"
                        :errors="v$.translateRate.$errors"
                        label="Коэфф. перевода ткани в ПС"
                        placeholder="Введите коэфф. перевода"
                        step="0.00000000001"
                        width="w-[230px]"
                    />

                </div>

                <div class="flex">

                    <!-- __ Производительность, м.п./ч. -->
                    <AppInputNumberSimpleTS
                        id="productivity"
                        v-model:inputNumber.number="v$.productivity.$model as unknown as number"
                        :errors="v$.productivity.$errors"
                        label="Производительность, м.п./ч."
                        placeholder="Введите производительность"
                        step="0.00000000001"
                        width="w-[230px]"
                    />

                    <!-- __ Количество рулонов ткани в буфере -->
                    <AppLabelCheckBoxTS
                        :state="textileLayersAmount === 1"
                        align="center"
                        label="Кол-во рулонов ткани в ПС, шт."
                        stateFalseChar="2"
                        stateFalseType="primary"
                        stateTrueChar="1"
                        stateTrueType="success"
                        width="w-[230px]"
                        @label-click="changeTextileLayersAmount"
                    />

                    <!--&lt;!&ndash; __ Получать среднюю производительность из статистики &ndash;&gt;-->
                    <!--<AppCheckboxLine-->
                    <!--    id="get_average_textile_productivity"-->
                    <!--    :disabled="true"-->
                    <!--    :width="'w-[300px]'"-->
                    <!--    label="Получать ср. производительность из статистики"-->
                    <!--/>-->

                </div>

                <div class="flex">

                    <div>
                        <!-- _ Статус -->
                        <div class="mt-8">
                            <!--suppress HtmlWrongAttributeValue -->
                            <AppCheckbox
                                id="active"
                                :checkboxData="checkboxDataStatus"
                                dir="horizontal"
                                inputType="radio"
                                legend="Статус"
                                type="secondary"
                                width="w-[230px]"
                                @checked="checkedHandlerStatus"
                            />
                        </div>
                    </div>

                    <div>
                        <!-- __ Редкость -->
                        <div class="mt-8">
                            <!--suppress HtmlWrongAttributeValue -->
                            <AppCheckbox
                                id="rarity"
                                :checkboxData="checkboxDataRarity"
                                :disabled="true"
                                dir="horizontal"
                                inputType="radio"
                                legend="Периодичность"
                                type="secondary"
                                width="w-[230px]"
                                @checked="checkedHandlerRarity"
                            />
                        </div>
                    </div>

                </div>

                <!-- __ Описание ПС -->
                <AppInputTextAreaSimpleTS
                    id="descr"
                    v-model:textValue.trim="v$.description.$model as unknown as string"
                    :rows=2
                    class="cursor-pointer"
                    height="min-h-[60px]"
                    label="Описание полотна стеганного"
                    placeholder="Заполните описание ПС"
                    width="w-[465px]"
                />

                <div class="flex mt-10 justify-center">

                    <!-- __ Кнопка Сохранить -->
                    <div>
                        <AppInputButton
                            id="submitButton"
                            func="submit"
                            title="Сохранить"
                            type="success"
                            width="w-[200px]"
                        />
                    </div>

                    <!-- __ Кнопка К Списку ПС -->
                    <router-link :to="{name: 'manufacture.cell.fabrics.show'}">
                        <AppInputButton
                            id="submitButton"
                            func="button"
                            title="К списку ПС"
                            type="primary"
                            width="w-[200px]"
                        />
                    </router-link>

                </div>

            </div>
        </form>

    </div>


    <!-- __ Callout -->
    <AppCallout
        :show="calloutShow"
        :text="calloutText"
        :type="calloutType"
    />


</template>

<script lang="ts" setup>
import { ref, reactive, watch, watchEffect, onMounted } from 'vue'
import type { IFabric } from '@/types'
import { useRoute, useRouter } from 'vue-router'

import { useVuelidate } from '@vuelidate/core'
import {
    helpers,
    required,
    integer,
    minLength,
    minValue,
    maxValue,
    between,
    // email,
    // sameAs
} from '@vuelidate/validators'

import { useFabricsStore } from '@/stores/FabricsStore.js'

import { NEW_FABRIC } from '@/app/constants/fabrics.js'

import { checkApiAnswer } from '@/app/helpers/helpers_checks.ts'
import { round } from '@/app/helpers/helpers_lib.js'


import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'
import AppCheckbox from '@/components/ui/checkboxes/AppCheckbox.vue'
import AppCallout from '@/components/ui/callouts/AppCallout.vue'
import AppInputTextTS from '@/components/ui/inputs/AppInputTextTS.vue'
import AppInputNumberSimpleTS from '@/components/ui/inputs/AppInputNumberSimpleTS.vue'
import AppInputTextAreaSimpleTS from '@/components/ui/inputs/AppInputTextAreaSimpleTS.vue'
import AppLabelCheckBoxTS from '@/components/ui/labels/AppLabelCheckBoxTS.vue'

// import AppCheckboxLine from '@/components/ui/checkboxes/AppCheckboxLine.vue'
// import AppInputTextAreaSimple from '@/components/ui/inputs/AppInputTextAreaSimple.vue'
// import AppCheckboxSimple from '@/components/ui/checkboxes/AppCheckboxSimple.vue'
// import AppInputNumber from '@/components/ui/inputs/AppInputNumber.vue'
// import AppSelect from '@/components/ui/selects/AppSelect.vue'
// import AppInputTextArea from '@/components/ui/inputs/AppInputTextArea.vue'
// import AppInputText from '@/components/ui/inputs/AppInputText.vue'
// import AppInputNumberSimple from '@/components/ui/inputs/AppInputNumberSimple.vue'


const fabricStore = useFabricsStore()

const route = useRoute()
// const router = useRouter()

const isLoading = ref(false)

// __ Получаем режим работы формы: создание или редактирование
const editMode = route.meta.mode === 'edit'

// __ Задаем пустое ПС для добавления
const fabric = ref<IFabric>(NEW_FABRIC)
// let fabric: IFabric = reactive(NEW_FABRIC)

// __ Получаем ПС
const getFabric = async () => {
    if (editMode) {
        fabric.value = await fabricStore.getFabricById(route.params.id)
        fabric.value.hand_length = fabric.value.hand_length === 0 ? fabric.value.buffer.average_length : fabric.value.hand_length
    }
}


// __ Количество рулонов в буфере
const getBufferRolls = () => {
    if (averageLength.value && translateRate.value) {
        return round(bufferAmount.value / averageLength.value / translateRate.value, 3)
    }
    return 0
}


// __ Формируем переменные реактивности
const code1C = ref(fabric.value.code_1C)
const name = ref(fabric.value.name)
const averageLength = ref(fabric.value.buffer.average_length)
const bufferAmount = ref(round(fabric.value.buffer.amount, 2))
const minRolls = ref(fabric.value.buffer.min_rolls)
const maxRolls = ref(fabric.value.buffer.max_rolls)
const optimalParty = ref(fabric.value.buffer.optimal_party)
const translateRate = ref(fabric.value.buffer.rate)
const productivity = ref(fabric.value.buffer.productivity)
const statistic = ref(fabric.value.statistic)
const description = ref(fabric.value.text.description ?? '')
const bufferRolls = ref(getBufferRolls())
const averageTextileLengthStatistic = ref(0)
const averageTextileLengthHand = ref(0)
const textileLayersAmount = ref(fabric.value.textile_layers_amount)


// __ Определяем константы правил валидации
const REQUIRED_MESSAGE = 'Поле обязательно'
const INTEGER_MESSAGE = 'Целое число'
const MIN_NAME_LENGTH = 25
const MIN_CODE_1C_LENGTH = 9
const MIN_TEXTILE_AVERAGE_LENGTH = 10
const MAX_TEXTILE_AVERAGE_LENGTH = 200
const MIN_BUFFER_AMOUNT = 0
const MAX_BUFFER_AMOUNT = 1
const MIN_ROLLS_AMOUNT = 1
const MAX_ROLLS_AMOUNT = 1
const OPTIMAL_PARTY_MIN_AMOUNT = 10
const RATE_MIN_AMOUNT = 1
const RATE_MAX_AMOUNT = 3
const PRODUCTIVITY_MIN_AMOUNT = 10

// __ Определяем объект валидации
const verify = {
    code1C,
    name,
    averageTextileLengthHand,
    bufferAmount,
    minRolls,
    maxRolls,
    optimalParty,
    translateRate,
    productivity,
    description,
    bufferRolls,
}

// Определяем правила валидации
const rules = {
    code1C: {
        required: helpers.withMessage(REQUIRED_MESSAGE, required),
        minLength: helpers.withMessage(`Мин. - ${MIN_CODE_1C_LENGTH} символов`, minLength(MIN_CODE_1C_LENGTH)),
    },
    name: {
        // $autoDirty: true,
        // $lazy: true,
        required: helpers.withMessage(REQUIRED_MESSAGE, required),
        minLength: helpers.withMessage(`Минимальная длина названия ПС - ${MIN_NAME_LENGTH} символов`, minLength(MIN_NAME_LENGTH)),
    },
    averageTextileLengthHand: {
        required: helpers.withMessage(REQUIRED_MESSAGE, required),
        minValue: helpers.withMessage(`Мин. значение - ${MIN_TEXTILE_AVERAGE_LENGTH} м.п.`, minValue(MIN_TEXTILE_AVERAGE_LENGTH)),
        maxValue: helpers.withMessage(`Макс. значение - ${MAX_TEXTILE_AVERAGE_LENGTH} м.п.`, maxValue(MAX_TEXTILE_AVERAGE_LENGTH)),
    },
    bufferAmount: {
        required: helpers.withMessage(REQUIRED_MESSAGE, required),
        minValue: helpers.withMessage(`Мин. значение - ${MIN_BUFFER_AMOUNT} м.п.`, minValue(MIN_BUFFER_AMOUNT)),
        // required: helpers.withMessage(REQUIRED_MESSAGE, required),
        // maxValue: helpers.withMessage(`Макс. значение - ${MAX_BUFFER_AMOUNT} м.п.`, maxValue(MAX_BUFFER_AMOUNT)),
    },
    minRolls: {
        required: helpers.withMessage(REQUIRED_MESSAGE, required),
        integer: helpers.withMessage(INTEGER_MESSAGE, integer),
        minValue: helpers.withMessage(`Мин. значение - ${MIN_ROLLS_AMOUNT} шт.`, minValue(MIN_ROLLS_AMOUNT)),
    },
    maxRolls: {
        required: helpers.withMessage(REQUIRED_MESSAGE, required),
        integer: helpers.withMessage(INTEGER_MESSAGE, integer),
        minValue: helpers.withMessage(`Мин. значение - ${MAX_ROLLS_AMOUNT} шт.`, minValue(MAX_ROLLS_AMOUNT)),
    },
    bufferRolls: {},
    optimalParty: {
        minValue: helpers.withMessage(`Мин. значение - ${OPTIMAL_PARTY_MIN_AMOUNT} м.п.`, minValue(OPTIMAL_PARTY_MIN_AMOUNT)),
    },
    translateRate: {
        required: helpers.withMessage(REQUIRED_MESSAGE, required),
        between: helpers.withMessage(`Значение в диапазоне от ${RATE_MIN_AMOUNT} до ${RATE_MAX_AMOUNT}`, between(RATE_MIN_AMOUNT, RATE_MAX_AMOUNT)),
    },
    productivity: {
        minValue: helpers.withMessage(`Мин. значение - ${PRODUCTIVITY_MIN_AMOUNT} м.п./ч.`, minValue(PRODUCTIVITY_MIN_AMOUNT)),
    },
    description: {},
    // comment: {},
    // note: {},
}

// attract: Оборачиваем в объект
const v$ = useVuelidate(rules, verify)


// attract: Формируем данные для отображения статуса
const checkboxDataStatus = {
    name: 'status',
    data: [
        {id: 1, name: 'Активный', checked: fabric.value.active},
        {id: 2, name: 'Архив', checked: !fabric.value.active},
    ]
}

// attract: Формируем данные для отображения редкости
const checkboxDataRarity = {
    name: 'rarity',
    data: [
        {id: 1, name: 'Регулярный', checked: fabric.value.rare},
        {id: 2, name: 'Редкий', checked: !fabric.value.rare},
    ]
}

// __ Меняем статус
const checkedHandlerStatus = (obj: { id: number }) => {
    console.log(obj)
    fabric.value.active = obj.id === 1
}

// __ Меняем редкость
const checkedHandlerRarity = (obj: { id: number }) => {
    console.log(obj)
    fabric.value.rare = obj.id === 1
}


// __ Callout для вывода ошибок и предупреждений
const calloutType = ref('danger')
const calloutText = ref('')
const calloutShow = ref(false)
const calloutClose = (delay = 5000) => setTimeout(() => calloutShow.value = false, delay) // закрываем callout


// __ Обрабатываем маяк получения статистики
const statisticHandler = async () => {
    statistic.value = !statistic.value
}


// __ Получаем данные для отображения, выносим в отдельный метод, потому что вызывается после монтирования
const setVariables = async () => {
    code1C.value = fabric.value.code_1C
    name.value = fabric.value.name
    bufferAmount.value = round(fabric.value.buffer.amount, 2)
    minRolls.value = fabric.value.buffer.min_rolls
    maxRolls.value = fabric.value.buffer.max_rolls
    optimalParty.value = fabric.value.buffer.optimal_party
    translateRate.value = fabric.value.buffer.rate
    productivity.value = fabric.value.buffer.productivity
    statistic.value = fabric.value.statistic
    description.value = fabric.value.text.description ?? ''
    bufferRolls.value = getBufferRolls()
    averageLength.value = fabric.value.buffer.average_length
    averageTextileLengthStatistic.value = 0
    averageTextileLengthHand.value = fabric.value.hand_length
    textileLayersAmount.value = fabric.value.textile_layers_amount
}

// __ Получаем среднюю длину ткани из статистики
const getAverageTextileLengthStatistic = async () => {
    averageTextileLengthStatistic.value = await fabricStore.getFabricsAverageLength(fabric.value.id)
    console.log('averageTextileLengthStatistic: ', averageTextileLengthStatistic.value)
}


// __ Меняем количество слоев ткани в ПС
const changeTextileLayersAmount = () => {
    if (textileLayersAmount.value === 1) {
        textileLayersAmount.value = 2
    } else {
        textileLayersAmount.value = 1
    }
}


// __ Отправляем форму на сервер
const formSubmit = async () => {

    // v$.value.$touch()                                // валидируем всю форму (обновляет маркеры валидации внутри объекта v$)
    const isFormCorrect = await v$.value.$validate()    // валидируем всю форму
    if (!isFormCorrect) return                          // это показатель ошибки

    // attract: Формируем массив для сохранения
    fabric.value.code_1C = code1C.value
    fabric.value.name = name.value
    fabric.value.buffer.amount = bufferAmount.value
    fabric.value.buffer.min_rolls = minRolls.value
    fabric.value.buffer.max_rolls = maxRolls.value
    fabric.value.buffer.optimal_party = optimalParty.value
    fabric.value.buffer.rate = translateRate.value
    fabric.value.buffer.productivity = productivity.value
    fabric.value.buffer.fabric_productivity = productivity.value
    fabric.value.statistic = statistic.value
    fabric.value.text.description = description.value
    // fabric.value.buffer.average_length = averageLength.value
    fabric.value.statistic_length = averageTextileLengthStatistic.value
    fabric.value.hand_length = averageTextileLengthHand.value
    fabric.value.textile_layers_amount = textileLayersAmount.value


    console.log('fabric: ', fabric.value)

    let res
    if (editMode) {
        console.log('update')
        res = await fabricStore.updateFabric(fabric.value)
    } else {
        console.log('create')
        res = await fabricStore.createFabric(fabric.value)
    }

    console.log('res', res)

    console.log(checkApiAnswer(res))

    if (checkApiAnswer(res).code === 0) {
        calloutType.value = 'success'
        calloutText.value = 'Сохранено успешно'
    } else {
        calloutType.value = 'danger'
        calloutText.value = 'Упс, что-то пошло не так...'
    }

    calloutShow.value = true
    calloutClose()


    // Пр-во попросило отключить автоматический переход
    // await router.push({name: 'manufacture.cell.fabrics.show'})      // переходим к списку ПС
}

// __ отслеживаем длину в рулонах
watchEffect(() => {
    bufferRolls.value = getBufferRolls()
})


onMounted(async () => {
    isLoading.value = true

    await getFabric()
    await setVariables()
    await getAverageTextileLengthStatistic()

    console.log('fabric: ', fabric.value)

    v$.value.$touch()   // __ Запускаем сразу валидацию формы

    isLoading.value = false
})

</script>

<style scoped>

</style>
