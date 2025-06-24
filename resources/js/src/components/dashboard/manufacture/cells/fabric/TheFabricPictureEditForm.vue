<template>

    <div class="m-2 p-2 w-max-fit">

        <form @submit.prevent="formSubmit">

            <div class="bg-slate-200 border-2 rounded-lg border-slate-400 p-2 size-fit">

                <div class="flex">

                    <!-- __ Название Рисунка -->
                    <AppInputText
                        id="name"
                        v-model.trim="v$.name.$model"
                        :errors="v$.name.$errors"
                        :value="v$.name.$model"
                        label="Название рисунка"
                        placeholder="Введите название рисунка ПС"
                        width="w-[120px]"
                    />

                    <!-- __ Длина стежка, мм -->
                    <AppInputNumberSimple
                        id="stitch_length"
                        v-model:inputNumber="v$.stitchLength.$model"
                        :errors="v$.stitchLength.$errors"
                        :value="v$.stitchLength.$model"
                        label="Длина стежка, мм"
                        placeholder="Введите длину стежка"
                        step="0.5"
                        width="w-[120px]"
                    />

                    <!-- __ Скорость стежков, шт./мин. -->
                    <AppInputNumberSimple
                        id="stitch_speed"
                        v-model:inputNumber="v$.stitchSpeed.$model"
                        :errors="v$.stitchSpeed.$errors"
                        :value="v$.stitchSpeed.$model"
                        label="Скорость стежков, шт./мин."
                        placeholder="Введите скорость стежков"
                        step="1"
                        width="w-[200px]"
                    />



                </div>

                <div class="flex">

                    <!-- __ Мгновенная скорость, м/ч -->
                    <AppInputNumberSimple
                        id="moment_speed"
                        v-model:inputNumber="v$.momentSpeed.$model"
                        :errors="v$.momentSpeed.$errors"
                        :value="v$.momentSpeed.$model"
                        label="Мгновенная скорость, м/ч"
                        placeholder="Введите мгновенную скорость"
                        step="1"
                        width="w-[200px]"
                    />

                    <!-- __ Количество челноков для Корейца, шт. -->
                    <AppInputNumberSimple
                        id="shuttle_amount"
                        v-model:inputNumber="v$.shuttleAmount.$model"
                        :errors="v$.shuttleAmount.$errors"
                        :value="v$.shuttleAmount.$model"
                        label="Кол-во челноков для Корейца, шт."
                        placeholder="Введите кол-во челноков"
                        step="1"
                        width="w-[245px]"
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
                                width="w-[460px]"
                                @checked="checkedHandlerStatus"
                            />
                        </div>
                    </div>

                </div>


                <div class="flex mt-4">

                    <!-- __ Основная СМ -->
                    <AppSelect
                        :select-data="mainMachine"
                        type="dark"
                        label="Основная СМ"
                        width="w-[230px]"
                        text-size="small"
                        @change="filterByMainMachine($event, 0)"
                    />

                    <!-- __ Схема стежки основной СМ -->
                    <AppSelect
                        :select-data="mainMachine"
                        type="dark"
                        label="Основная СМ"
                        width="w-[230px]"
                        text-size="small"
                        @change="filterByMainMachine($event, 0)"
                    />

                </div>


                <!-- attract: Описание ПС -->
                <AppInputTextAreaSimple
                    id="descr"
                    v-model.trim="v$.description.$model"
                    :rows=2
                    :value="v$.description.$model"
                    class="cursor-pointer"
                    height="min-h-[60px]"
                    label="Описание рисунка полотна стеганного"
                    placeholder="Заполните описание рисунка"
                    width="w-[465px]"
                />

                <div class="flex mt-10">

                    <div>
                        <AppInputButton
                            id="submitButton"
                            func="submit"
                            title="Сохранить"
                            type="success"
                            width="w-[230px]"
                        />
                    </div>

                    <router-link :to="{name: 'manufacture.cell.fabric.pictures.show'}">
                        <AppInputButton
                            id="submitButton"
                            func="button"
                            title="К списку рисунков"
                            type="primary"
                            width="w-[230px]"
                        />
                    </router-link>


                </div>

            </div>
        </form>

    </div>
</template>

<script setup>
import {ref, reactive, watch, watchEffect, onMounted} from 'vue'

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

import {useFabricsStore} from '@/stores/FabricsStore.js'

import {
    NEW_FABRIC_PICTURE,
    FABRIC_PAGE_MODE,
    FABRIC_MACHINES
} from '@/app/constants/fabrics.js'

import {round} from '@/app/helpers/helpers_lib.js'

import AppInputText from '@/components/ui/inputs/AppInputText.vue'
import AppInputNumberSimple from '@/components/ui/inputs/AppInputNumberSimple.vue'
import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'
import AppCheckbox from '@/components/ui/checkboxes/AppCheckbox.vue'
import AppCheckboxLine from '@/components/ui/checkboxes/AppCheckboxLine.vue'
import AppInputTextAreaSimple from '@/components/ui/inputs/AppInputTextAreaSimple.vue'
import AppSelectSimple from "@/components/ui/selects/AppSelectSimple.vue";
import AppSelect from "@/components/ui/selects/AppSelect.vue";

const fabricStore = useFabricsStore()

const route = useRoute()

// __ Получаем СМ
const machines = await fabricStore.getFabricsMachines()
console.log('machines: ', machines)

// __ Получаем список схем рисунков ПС
const schemas = await fabricStore.getFabricPictureSchemas()
console.log('schemas: ', schemas)

// console.log('meta', route.meta.mode)

// attract: Получаем режим работы формы: создание или редактирование
const editMode = route.meta.mode === FABRIC_PAGE_MODE.EDIT

console.log('route: ', route)

// attract: Задаем пустой рисунок ПС для добавления
const fabricPicture = reactive(NEW_FABRIC_PICTURE)

// attract: Получаем ПС
if (editMode) {
    const fabricPictureServer = reactive(await fabricStore.getFabricPictureById(route.params.id))

    fabricPicture.id = fabricPictureServer.id
    fabricPicture.active = fabricPictureServer.active
    fabricPicture.name = fabricPictureServer.name
    fabricPicture.stitch_length = fabricPictureServer.stitch_length
    fabricPicture.stitch_speed = fabricPictureServer.stitch_speed
    fabricPicture.moment_speed = fabricPictureServer.moment_speed
    fabricPicture.shuttle_amount = fabricPictureServer.shuttle_amount
    fabricPicture.description = fabricPictureServer.description
    fabricPicture.fabricMainMachineId = fabricPictureServer.machines.fabricMainMachine.machine.id
    fabricPicture.fabricMainMachineSchemaId = fabricPictureServer.machines.fabricMainMachine.schema.id
    fabricPicture.fabricAltMachineId_1 = fabricPictureServer.machines.fabricAltMachine_1.machine.id
    fabricPicture.fabricAltMachineSchemaId_1 = fabricPictureServer.machines.fabricAltMachine_1.schema.id
    fabricPicture.fabricAltMachineId_2 = fabricPictureServer.machines.fabricAltMachine_2.machine.id
    fabricPicture.fabricAltMachineSchemaId_2 = fabricPictureServer.machines.fabricAltMachine_2.schema.id
    fabricPicture.fabricAltMachineId_3 = fabricPictureServer.machines.fabricAltMachine_3.machine.id
    fabricPicture.fabricAltMachineSchemaId_3 = fabricPictureServer.machines.fabricAltMachine_3.schema.id

}

console.log('fabricPicture: ', fabricPicture)


// attract: Формируем массив для реактивности
const name = ref(fabricPicture.name)
const stitchLength = ref(fabricPicture.stitch_length)
const stitchSpeed = ref(fabricPicture.stitch_speed)
const momentSpeed = ref(fabricPicture.moment_speed)
const shuttleAmount = ref(fabricPicture.shuttle_amount)


// attract: Определяем константы правил валидации
const REQUIRED_MESSAGE = 'Поле обязательно'
const INTEGER_MESSAGE = 'Целое число'
const MIN_NAME_LENGTH = 2
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

// attract: Определяем объект валидации
const verify = {
    name,
    stitchLength,
    stitchSpeed,
    momentSpeed,
    shuttleAmount
}

// Определяем правила валидации
const rules = {
    name: {
        required: helpers.withMessage(REQUIRED_MESSAGE, required),
        minLength: helpers.withMessage(`Минимальная длина рисунка ПС - ${MIN_NAME_LENGTH} символа(ов)`, minLength(MIN_NAME_LENGTH)),
    },
    stitchLength: {},
    stitchSpeed: {},
    momentSpeed: {},
    shuttleAmount: {},
    description: {},
    // averageLength: {
    //     required: helpers.withMessage(REQUIRED_MESSAGE, required),
    //     minValue: helpers.withMessage(`Мин. значение - ${MIN_TEXTILE_AVERAGE_LENGTH} м.п.`, minValue(MIN_TEXTILE_AVERAGE_LENGTH)),
    //     maxValue: helpers.withMessage(`Макс. значение - ${MAX_TEXTILE_AVERAGE_LENGTH} м.п.`, maxValue(MAX_TEXTILE_AVERAGE_LENGTH)),
    // },
    // bufferAmount: {
    //     required: helpers.withMessage(REQUIRED_MESSAGE, required),
    //     minValue: helpers.withMessage(`Мин. значение - ${MIN_BUFFER_AMOUNT} м.п.`, minValue(MIN_BUFFER_AMOUNT)),
    //     // required: helpers.withMessage(REQUIRED_MESSAGE, required),
    //     // maxValue: helpers.withMessage(`Макс. значение - ${MAX_BUFFER_AMOUNT} м.п.`, maxValue(MAX_BUFFER_AMOUNT)),
    // },
    // minRolls: {
    //     required: helpers.withMessage(REQUIRED_MESSAGE, required),
    //     integer: helpers.withMessage(INTEGER_MESSAGE, integer),
    //     minValue: helpers.withMessage(`Мин. значение - ${MIN_ROLLS_AMOUNT} шт.`, minValue(MIN_ROLLS_AMOUNT)),
    // },
    // maxRolls: {
    //     required: helpers.withMessage(REQUIRED_MESSAGE, required),
    //     integer: helpers.withMessage(INTEGER_MESSAGE, integer),
    //     minValue: helpers.withMessage(`Мин. значение - ${MAX_ROLLS_AMOUNT} шт.`, minValue(MAX_ROLLS_AMOUNT)),
    // },
    // bufferRolls: {},
    // optimalParty: {
    //     minValue: helpers.withMessage(`Мин. значение - ${OPTIMAL_PARTY_MIN_AMOUNT} м.п.`, minValue(OPTIMAL_PARTY_MIN_AMOUNT)),
    // },
    // translateRate: {
    //     required: helpers.withMessage(REQUIRED_MESSAGE, required),
    //     between: helpers.withMessage(`Значение в диапазоне от ${RATE_MIN_AMOUNT} до ${RATE_MAX_AMOUNT}`, between(RATE_MIN_AMOUNT, RATE_MAX_AMOUNT)),
    // },
    // productivity: {
    //     minValue: helpers.withMessage(`Мин. значение - ${PRODUCTIVITY_MIN_AMOUNT} м.п./ч.`, minValue(PRODUCTIVITY_MIN_AMOUNT)),
    // },

    // comment: {},
    // note: {},
}

// attract: Оборачиваем в объект
const v$ = useVuelidate(rules, verify)


// attract: Формируем данные для отображения статуса
const checkboxDataStatus = {
    name: 'status',
    data: [
        {id: 1, name: 'Активный', checked: fabricPicture.active},
        {id: 2, name: 'Архив', checked: !fabricPicture.active},
    ]
}


// __ Селекты для фильтрации СМ
const machinesData = [
    {id: 0, name: 'Все', selected: true, disabled: false},
    {id: FABRIC_MACHINES.AMERICAN.ID, name: FABRIC_MACHINES.AMERICAN.NAME, selected: false, disabled: false},
    {id: FABRIC_MACHINES.GERMAN.ID, name: FABRIC_MACHINES.GERMAN.NAME, selected: false, disabled: false},
    {id: FABRIC_MACHINES.CHINA.ID, name: FABRIC_MACHINES.CHINA.NAME, selected: false, disabled: false},
    {id: FABRIC_MACHINES.KOREAN.ID, name: FABRIC_MACHINES.KOREAN.NAME, selected: false, disabled: false},
]

const mainMachine = {name: 'mainMachine', data: machinesData}



// const checkboxDataRarity = {
//     name: 'rarity',
//     data: [
//         {id: 1, name: 'Регулярный', checked: fabric.rare},
//         {id: 2, name: 'Редкий', checked: !fabric.rare},
//     ]
// }

// attract: Меняем статус
const checkedHandlerStatus = (obj) => {
    console.log(obj)
    fabricPicture.active = obj.id === 1
}

// // attract: Меняем редкость
// const checkedHandlerRarity = (obj) => {
//     console.log(obj)
//     fabric.rare = obj.id === 1
// }





// __ Меняем СМ
const filterByMainMachine = (event, machineOrder /* 0 - основная СМ, 1 - альт СМ 1, 2 - альт СМ 2 */) => {
    // switch (machineOrder) {
    //     case 0:
    //         mainMachineFilter.value = event.id
    //         break
    //     case 1:
    //         altMachineFilter_1.value = event.id
    //         break
    //     case 2:
    //         altMachineFilter_2.value = event.id
    //         break
    // }
}






// Отправляем форму на сервер
const formSubmit = async () => {


    //
    //
    //
    // // v$.value.$touch()                                // валидируем всю форму (обновляет маркеры валидации внутри объекта v$)
    // const isFormCorrect = await v$.value.$validate()    // валидируем всю форму
    // if (!isFormCorrect) return                          // это показатель ошибки
    //
    // // attract: Формируем массив для сохранения
    // fabric.code_1C = code1C.value
    // fabric.name = name.value
    // fabric.buffer.average_length = averageLength.value
    // fabric.buffer.amount = bufferAmount.value
    // fabric.buffer.min_rolls = minRolls.value
    // fabric.buffer.max_rolls = maxRolls.value
    // fabric.buffer.optimal_party = optimalParty.value
    // fabric.buffer.rate = translateRate.value
    // fabric.buffer.productivity = productivity.value
    // fabric.text.description = description.value
    //
    // console.log('fabric: ', fabric)
    //
    // let res
    // if (editMode) {
    //     console.log('update')
    //     // res = await fabricStore.updateFabric(fabric)
    // } else {
    //     console.log('create')
    //     // res = await fabricStore.createFabric(fabric)
    // }
    // console.log('res', res)
    //
    //



    // Пр-во попросило отключить автоматический переход
    // await router.push({name: 'manufacture.cell.fabrics.show'})      // переходим к списку ПС
}












// attract: отслеживаем длину в рулонах
watchEffect(() => {
    // bufferRolls.value = getBufferRolls()
})

// attract: Запускаем сразу валидацию формы
onMounted(() => {
    v$.value.$touch()
})

</script>

<style scoped>

</style>
