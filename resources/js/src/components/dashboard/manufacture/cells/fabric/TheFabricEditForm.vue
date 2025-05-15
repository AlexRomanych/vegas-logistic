<template>

    <div class="m-2 p-2 w-max-fit">

        <form @submit.prevent="formSubmit">

            <div class="bg-slate-200 border-2 rounded-lg border-slate-400 p-2 size-fit">

                <div class="flex">

                    <!-- attract: Код по 1С -->
                    <AppInputText
                        id="code_1C"
                        v-model.trim="v$.code1C.$model"
                        :errors="v$.code1C.$errors"
                        :value="v$.code1C.$model"
                        label="Код по 1С"
                        placeholder="Код по 1С"
                        width="w-[100px]"

                    />

                    <!-- attract: Название ПС -->
                    <AppInputText
                        id="name"
                        v-model.trim="v$.name.$model"
                        :errors="v$.name.$errors"
                        :value="v$.name.$model"
                        label="Название ПС"
                        placeholder="Введите название ПС"
                        width="w-[350px]"
                    />
                </div>

                <div class="flex">

                    <!-- attract: Средняя длина рулона ткани, м.п. -->
                    <AppInputNumberSimple
                        id="average_textile_length"
                        v-model:inputNumber="v$.averageLength.$model"
                        :errors="v$.averageLength.$errors"
                        :value="v$.averageLength.$model"
                        label="Средняя длина рулона ткани, м.п."
                        placeholder="Введите ср. длину рул. ткани"
                        step="0.001"
                        width="w-[230px]"
                    />


                    <!-- attract: Получать среднюю длину ткани из статистики -->
                    <AppCheckboxLine
                        id="get_average_textile_length"
                        :disabled="true"
                        :width="'w-[300px]'"
                        label="Получать среднюю длину ткани из статистики"
                    />

                </div>


                <div class="flex">

                    <!-- attract: Буфер, м.п. -->
                    <AppInputNumberSimple
                        id="buffer_meters"
                        v-model:inputNumber="v$.bufferAmount.$model"
                        :errors="v$.bufferAmount.$errors"
                        :value="v$.bufferAmount.$model"
                        label="Буфер, м.п."
                        placeholder="Введите остаток ПС (буфер)"
                        step="0.001"
                        width="w-[230px]"
                    />

                    <!-- attract: Буфер, рул. -->
                    <AppInputNumberSimple
                        id="buffer_rolls"
                        v-model:inputNumber="v$.bufferRolls.$model"
                        :disabled="true"
                        :errors="v$.bufferRolls.$errors"
                        :value="v$.bufferRolls.$model"
                        label="Буфер, рул."
                        placeholder="Введите остаток ПС (буфер)"
                        step="0.1"
                        width="w-[230px]"
                    />
                </div>


                <div class="flex">

                    <!-- attract: Мин. запас ПС, рул. -->
                    <AppInputNumberSimple
                        id="min_buffer_rolls"
                        v-model:inputNumber="v$.minRolls.$model"
                        :errors="v$.minRolls.$errors"
                        :value="v$.minRolls.$model"
                        label="Минимальный запас ПС, рул."
                        placeholder="Введите мин. запас"
                        step="1"
                        width="w-[230px]"
                    />

                    <!-- attract: Макс. запас ПС, рул. -->
                    <AppInputNumberSimple
                        id="max_buffer_rolls"
                        v-model:inputNumber="v$.maxRolls.$model"
                        :errors="v$.maxRolls.$errors"
                        :value="v$.maxRolls.$model"
                        label="Максимальный запас ПС, рул."
                        placeholder="Введите макс. запас"
                        step="1"
                        width="w-[230px]"
                    />

                </div>

                <div class="flex">

                    <!-- attract: Оптимальная партия для запуска, м.п. (ОПЗ) -->
                    <AppInputNumberSimple
                        id="optimal_party"
                        v-model:inputNumber="v$.optimalParty.$model"
                        :errors="v$.optimalParty.$errors"
                        :value="v$.optimalParty.$model"
                        label="Оптим. партия для запуска, м.п."
                        placeholder="Введите ОПЗ"
                        step="1"
                        width="w-[230px]"
                    />

                    <!-- attract: Коэффициент перевода ткани в ПС -->
                    <AppInputNumberSimple
                        id="rate"
                        v-model:inputNumber="v$.translateRate.$model"
                        :errors="v$.translateRate.$errors"
                        :value="v$.translateRate.$model"
                        label="Коэфф. перевода ткани в ПС"
                        placeholder="Введите коэфф. перевода"
                        step="0.00000000001"
                        width="w-[230px]"
                    />

                </div>

                <div class="flex">

                    <!-- attract: Производительность, м.п./ч. -->
                    <AppInputNumberSimple
                        id="productivity"
                        v-model:inputNumber="v$.productivity.$model"
                        :errors="v$.productivity.$errors"
                        :value="v$.productivity.$model"
                        label="Производительность, м.п./ч."
                        placeholder="Введите производительность"
                        step="0.00000000001"
                        width="w-[230px]"
                    />

                    <!-- attract: Получать среднюю производительность из статистики -->
                    <AppCheckboxLine
                        id="get_average_textile_productivity"
                        :disabled="true"
                        :width="'w-[300px]'"
                        label="Получать ср. производительность из статистики"
                    />

                </div>

                <div class="flex">

                    <div>
                        <!-- attract: Статус -->
                        <div class="mt-8">
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
                        <!-- attract: редкость -->
                        <div class="mt-8">
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

                <!-- attract: Описание ПС -->
                <AppInputTextAreaSimple
                    id="descr"
                    v-model.trim="v$.description.$model"
                    :value="v$.description.$model"
                    :rows=2
                    class="cursor-pointer"
                    height="min-h-[60px]"
                    label="Описание полотна стеганного"
                    placeholder="Заполните описание ПС"
                    width="w-[465px]"
                />

                <div class="flex mt-10">

                    <div>
                        <AppInputButton
                            id="submitButton"
                            func="submit"
                            title="Сохранить"
                            type="success"
                            width="w-[200px]"
                        />
                    </div>

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
</template>

<script setup>
import {ref, reactive, watch, watchEffect} from 'vue'

import {useRoute, useRouter} from 'vue-router'

import {useVuelidate} from '@vuelidate/core'
import {
    helpers,
    required,
    integer,
    minLength,
    minValue,
    maxValue,
    between,
    email,
    sameAs
} from '@vuelidate/validators'

import {useFabricsStore} from '/resources/js/src/stores/FabricsStore.js'

import {NEW_FABRIC} from '/resources/js/src/app/constants/fabrics.js'

import {round} from '/resources/js/src/app/helpers/helpers_lib.js'

import AppInputText from '/resources/js/src/components/ui/inputs/AppInputText.vue'
import AppInputNumberSimple from '/resources/js/src/components/ui/inputs/AppInputNumberSimple.vue'
import AppInputButton from '/resources/js/src/components/ui/inputs/AppInputButton.vue'
import AppCheckbox from '/resources/js/src/components/ui/checkboxes/AppCheckbox.vue'
import AppCheckboxLine from '/resources/js/src/components/ui/checkboxes/AppCheckboxLine.vue'
import AppInputTextAreaSimple from '/resources/js/src/components/ui/inputs/AppInputTextAreaSimple.vue'

// import AppCheckboxSimple from '/resources/js/src/components/ui/checkboxes/AppCheckboxSimple.vue'
// import AppInputNumber from '/resources/js/src/components/ui/inputs/AppInputNumber.vue'
// import AppSelect from '/resources/js/src/components/ui/selects/AppSelect.vue'
// import AppInputTextArea from '/resources/js/src/components/ui/inputs/AppInputTextArea.vue'

const fabricStore = useFabricsStore()

const route = useRoute()
const router = useRouter()

// console.log('meta', route.meta.mode)

// attract: Получаем режим работы формы: создание или редактирование
const editMode = route.meta.mode === 'edit'

console.log('route: ', route)

// attract: Задаем пустое ПС для добавления
let fabric = reactive(NEW_FABRIC)

// attract: Получаем ПС
if (editMode) {
    fabric = reactive(await fabricStore.getFabricById(route.params.id))
}

console.log('fabric: ', fabric)


// attract: Количество рулонов в буфере
const getBufferRolls = () => {
    if (averageLength.value && translateRate.value) {
        return round(bufferAmount.value / averageLength.value / translateRate.value, 3)
    }
    return 0
}


// attract: Формируем массив для реактивности
const code1C = ref(fabric.code_1C)
const name = ref(fabric.name)
const averageLength = ref(fabric.buffer.average_length)
const bufferAmount = ref(round(fabric.buffer.amount, 3))
const minRolls = ref(fabric.buffer.min_rolls)
const maxRolls = ref(fabric.buffer.max_rolls)
const optimalParty = ref(fabric.buffer.optimal_party)
const translateRate = ref(fabric.buffer.rate)
const productivity = ref(fabric.buffer.productivity)
const description = ref(fabric.text.description)
const bufferRolls = ref(getBufferRolls())


// attract: Определяем константы правил валидации
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
const RATE_MAX_AMOUNT = 2
const PRODUCTIVITY_MIN_AMOUNT = 10

// attract: Определяем объект валидации
const verify = {
    code1C,
    name,
    averageLength,
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
    averageLength: {
        required: helpers.withMessage(REQUIRED_MESSAGE, required),
        minValue: helpers.withMessage(`Мин. значение - ${MIN_TEXTILE_AVERAGE_LENGTH} м.п.`, minValue(MIN_TEXTILE_AVERAGE_LENGTH)),
        maxValue: helpers.withMessage(`Макс. значение - ${MAX_TEXTILE_AVERAGE_LENGTH} м.п.`, maxValue(MAX_TEXTILE_AVERAGE_LENGTH)),
    },
    bufferAmount: {
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
        {id: 1, name: 'Активный', checked: fabric.active},
        {id: 2, name: 'Архив', checked: !fabric.active},
    ]
}

// attract: Формируем данные для отображения редкости
const checkboxDataRarity = {
    name: 'rarity',
    data: [
        {id: 1, name: 'Регулярный', checked: fabric.rare},
        {id: 2, name: 'Редкий', checked: !fabric.rare},
    ]
}

// attract: Меняем статус
const checkedHandlerStatus = (obj) => {
    console.log(obj)
    fabric.active = obj.id === 1
}

// attract: Меняем редкость
const checkedHandlerRarity = (obj) => {
    console.log(obj)
    fabric.rare = obj.id === 1
}

// Отправляем форму на сервер
const formSubmit = async () => {

    // v$.value.$touch()                                // валидируем всю форму (обновляет маркеры валидации внутри объекта v$)
    const isFormCorrect = await v$.value.$validate()    // валидируем всю форму
    if (!isFormCorrect) return                          // это показатель ошибки

    // attract: Формируем массив для реактивности
    fabric.code_1C = code1C.value
    fabric.name = name.value
    fabric.buffer.average_length = averageLength.value
    fabric.buffer.amount = bufferAmount.value
    fabric.buffer.min_rolls = minRolls.value
    fabric.buffer.max_rolls = maxRolls.value
    fabric.buffer.optimal_party = optimalParty.value
    fabric.buffer.rate = translateRate.value
    fabric.buffer.productivity = productivity.value
    fabric.text.description = description.value

    console.log('fabric: ', fabric)

    let res
    if (editMode) {
        console.log('update')
        res = await fabricStore.updateFabric(fabric)
    } else {
        console.log('create')
        res = await fabricStore.createFabric(fabric)
    }
    console.log('res', res)

    await router.push({name: 'manufacture.cell.fabrics.show'})      // переходим к списку сотрудников
}


watchEffect(() => {
    bufferRolls.value = getBufferRolls()
})

</script>

<style scoped>

</style>
