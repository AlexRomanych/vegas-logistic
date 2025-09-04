<template>

    <div v-if="!isLoading" class="m-2 p-2 w-max-fit">

        <form @submit.prevent="formSubmit">

            <div class="bg-slate-200 border-2 rounded-lg border-slate-400 p-2 size-fit">

                <div class="flex">

                    <!-- __ Название Рисунка -->
                    <AppInputTextTS
                        id="name"
                        v-model:textValue.trim="v$.name.$model as unknown as string"
                        :errors="v$.name.$errors"
                        label="Название рисунка"
                        placeholder="Рисунок ПС"
                        width="w-[120px]"
                        @input="inputNameHandler"
                    />

                    <!-- __ Длина стежка, мм -->
                    <AppInputNumberSimpleTS
                        id="stitch_length"
                        v-model:inputNumber.number="v$.stitchLength.$model as unknown as number"
                        :errors="v$.stitchLength.$errors"
                        label="Длина стежка, мм"
                        placeholder="Введите длину стежка"
                        step="0.5"
                        width="w-[120px]"
                    />

                    <!-- __ Скорость стежков, шт./мин. -->
                    <AppInputNumberSimpleTS
                        id="stitch_speed"
                        v-model:inputNumber.number="v$.stitchSpeed.$model as unknown as number"
                        :errors="v$.stitchSpeed.$errors"
                        label="Скорость стежков, шт./мин."
                        placeholder="Введите скорость стежков"
                        step="1"
                        width="w-[200px]"
                    />

                </div>

                <div class="flex">

                    <!-- __ Мгновенная скорость, м/ч -->
                    <AppInputNumberSimpleTS
                        id="moment_speed"
                        v-model:inputNumber.number="v$.momentSpeed.$model as unknown as number"
                        :errors="v$.momentSpeed.$errors"
                        label="Мгновенная скорость, м/ч"
                        placeholder="Введите мгновенную скорость"
                        step="1"
                        width="w-[200px]"
                    />

                    <!-- __ Количество челноков для Корейца, шт. -->
                    <!--<div v-if="showShuttleAmountField">-->
                    <AppInputNumberSimpleTS
                        id="shuttle_amount"
                        v-model:inputNumber.number="v$.shuttleAmount.$model as unknown as number"
                        :disabled="!showShuttleAmountField"
                        :errors="v$.shuttleAmount.$errors"
                        label="Кол-во челноков для Корейца, шт."
                        placeholder="Введите кол-во челноков"
                        step="1"
                        width="w-[245px]"
                    />
                    <!--</div>-->

                </div>

                <div class="flex">
                    <!-- __ Производительность на основной СМ, м.п./час -->
                    <AppInputNumberSimpleTS
                        id="productivity"
                        v-model:inputNumber.number="v$.productivity.$model as unknown as number"
                        :errors="v$.productivity.$errors"
                        label="Производительность на основной СМ, м.п./час"
                        placeholder="Введите производительность"
                        step="1"
                        width="w-[453px]"
                    />
                </div>


                <div class="flex">
                    <div>
                        <!-- __ Статус -->
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

                <div class="mt-4">
                    <div v-for="(machine, index) in machinesRenderList" :key="index">

                        <div v-if="machine.visible" class="flex mt-2">

                            <!-- __ СМ -->
                            <AppSelect
                                :label="machine.machineLabel"
                                :select-data="getMachineSelect(machine, index)"
                                :type="machine.type"
                                text-size="small"
                                width="w-[230px]"
                                @change="handleMachine($event, index)"
                            />

                            <!-- __ Схема игл -->
                            <AppSelect
                                :label="machine.schemaLabel"
                                :select-data="getMachineSchemaSelect(machine, index)"
                                :type="machine.type"
                                text-size="small"
                                width="w-[230px]"
                                @change="handleMachineSchema($event, index)"
                            />
                        </div>
                    </div>

                </div>

                <!-- __ Описание ПС -->
                <AppInputTextAreaSimple
                    id="descr"
                    v-model.trim="v$.description.$model"
                    :rows=2
                    :value="v$.description.$model as unknown as string"
                    class="cursor-pointer"
                    height="min-h-[60px]"
                    label="Описание рисунка полотна стеганного"
                    placeholder="Заполните описание рисунка"
                    width="w-[465px]"
                />


                <div class="flex mt-10">

                    <!-- __ Сохранить -->
                    <div>
                        <AppInputButton
                            id="submitButton"
                            func="submit"
                            title="Сохранить"
                            type="success"
                            width="w-[230px]"
                        />
                    </div>

                    <!-- __ К списку рисунков -->
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


    <!-- __ Асинхронное модальное окно -->
    <AppModalAsyncMultiLine
        ref="appModalAsync"
        :text="modalText"
        :type="modalType"
        mode="inform"
    />

    <!-- __ Callout -->
    <AppCallout
        :show="calloutShow"
        :text="calloutText"
        :type="calloutType"
    />

</template>

<script lang="ts" setup>
import { ref, reactive, watch, onMounted } from 'vue'

import type {
    ICheckboxData,
    IFabricMachine,
    IFabricSchema,
    ISelectData,
    ISelectDataItem
} from '@/types'

import { useRoute } from 'vue-router'

import { useVuelidate } from '@vuelidate/core'
import {
    helpers,
    required,
    minLength,
    minValue,
    // integer,
    // maxValue,
    // between,
    // email,
    // sameAs
} from '@vuelidate/validators'

import { useFabricsStore } from '@/stores/FabricsStore.js'

import {
    NEW_FABRIC_PICTURE,
    FABRIC_PAGE_MODE,
    FABRIC_MACHINES,
} from '@/app/constants/fabrics.js'

import AppInputButton from '@/components/ui/inputs/AppInputButton.vue'
import AppCheckbox from '@/components/ui/checkboxes/AppCheckbox.vue'
import AppInputTextAreaSimple from '@/components/ui/inputs/AppInputTextAreaSimple.vue'
import AppSelect from '@/components/ui/selects/AppSelect.vue'
import AppModalAsyncMultiLine from '@/components/ui/modals/AppModalAsyncMultiline.vue'
import AppCallout from '@/components/ui/callouts/AppCallout.vue'
import AppInputTextTS from '@/components/ui/inputs/AppInputTextTS.vue'
import AppInputNumberSimpleTS from '@/components/ui/inputs/AppInputNumberSimpleTS.vue'


// line -----------------------------------------------------------------------------------------------------------
// line ------------- Объявление типов ----------------------------------------------------------------------------
// line -----------------------------------------------------------------------------------------------------------
interface IMachineState {
    machineId: number
    schemaId: number
    visible: boolean
    machineLabel: string
    schemaLabel: string
    type: string
}

// line -----------------------------------------------------------------------------------------------------------


const fabricStore = useFabricsStore()

const route = useRoute()
// console.log('route: ', route)
// console.log('meta', route.meta.mode)

const isLoading = ref(false)

// __ Определяем переменные
const machines = ref<IFabricMachine[]>([])
const schemas = ref<IFabricSchema[]>([])


// __ Получаем СМ
const getMachines = async () => (machines.value = await fabricStore.getFabricsMachines()) //.filter(machine => machine.id)

// __ Получаем список схем рисунков ПС
const getSchemas = async () => (schemas.value = await fabricStore.getFabricPictureSchemas())


// __ Получаем режим работы формы: создание или редактирование
const editMode = route.meta.mode === FABRIC_PAGE_MODE.EDIT

// __ Задаем пустой рисунок ПС для добавления
const fabricPicture = reactive(JSON.parse(JSON.stringify(NEW_FABRIC_PICTURE)))
fabricPicture.fabricMainMachineId = 1                   // По умолчанию: СМ, так как убираем нулевое значение
fabricPicture.fabricMainMachineSchemaId = 1             // По умолчанию: Схема, так как убираем нулевое значение

// __ Получаем ПС
if (editMode) {
    const fabricPictureServer = reactive(await fabricStore.getFabricPictureById(route.params.id))

    fabricPicture.id = fabricPictureServer.id
    fabricPicture.active = fabricPictureServer.active
    fabricPicture.name = fabricPictureServer.name
    fabricPicture.stitch_length = fabricPictureServer.stitch_length
    fabricPicture.stitch_speed = fabricPictureServer.stitch_speed
    fabricPicture.moment_speed = fabricPictureServer.moment_speed
    fabricPicture.shuttle_amount = fabricPictureServer.shuttle_amount
    fabricPicture.productivity = fabricPictureServer.productivity
    fabricPicture.description = fabricPictureServer.description
    fabricPicture.fabricMainMachineId = fabricPictureServer.machines.fabricMainMachine.machine.id === 0 ? 1 : fabricPictureServer.machines.fabricMainMachine.machine.id
    fabricPicture.fabricMainMachineSchemaId = fabricPictureServer.machines.fabricMainMachine.schema.id === 0 ? 1 : fabricPictureServer.machines.fabricMainMachine.schema.id
    fabricPicture.fabricAltMachineId_1 = fabricPictureServer.machines.fabricAltMachine_1.machine.id
    fabricPicture.fabricAltMachineSchemaId_1 = fabricPictureServer.machines.fabricAltMachine_1.schema.id
    fabricPicture.fabricAltMachineId_2 = fabricPictureServer.machines.fabricAltMachine_2.machine.id
    fabricPicture.fabricAltMachineSchemaId_2 = fabricPictureServer.machines.fabricAltMachine_2.schema.id
    fabricPicture.fabricAltMachineId_3 = fabricPictureServer.machines.fabricAltMachine_3.machine.id
    fabricPicture.fabricAltMachineSchemaId_3 = fabricPictureServer.machines.fabricAltMachine_3.schema.id
}

// console.log('fabricPicture: ', fabricPicture)

// __ Формируем массив для реактивности
const name = ref(fabricPicture.name)
const stitchLength = ref(fabricPicture.stitch_length)
const stitchSpeed = ref(fabricPicture.stitch_speed)
const momentSpeed = ref(fabricPicture.moment_speed)
const shuttleAmount = ref(fabricPicture.shuttle_amount)
const productivity = ref(fabricPicture.productivity)
const description = ref(fabricPicture.description)

// __ Определяем константы правил валидации
const REQUIRED_MESSAGE = 'Поле обязательно'
const MIN_NAME_LENGTH = 2
// const INTEGER_MESSAGE = 'Целое число'
// const MIN_CODE_1C_LENGTH = 9
// const MIN_TEXTILE_AVERAGE_LENGTH = 10
// const MAX_TEXTILE_AVERAGE_LENGTH = 200
// const MIN_BUFFER_AMOUNT = 0
// const MAX_BUFFER_AMOUNT = 1
// const MIN_ROLLS_AMOUNT = 1
// const MAX_ROLLS_AMOUNT = 1
// const OPTIMAL_PARTY_MIN_AMOUNT = 10
// const RATE_MIN_AMOUNT = 1
// const RATE_MAX_AMOUNT = 3
// const PRODUCTIVITY_MIN_AMOUNT = 10
const MIN_PRODUCTIVITY = 0.1

// __ Определяем объект валидации
const verify = {
    name,
    stitchLength,
    stitchSpeed,
    momentSpeed,
    shuttleAmount,
    productivity,
    description
}

// __ Определяем правила валидации
const rules = {
    name: {
        required: helpers.withMessage(REQUIRED_MESSAGE, required),
        minLength: helpers.withMessage(`Минимальная длина рисунка ПС - ${MIN_NAME_LENGTH} символа(ов)`, minLength(MIN_NAME_LENGTH)),
    },
    stitchLength: {},
    stitchSpeed: {},
    momentSpeed: {},
    shuttleAmount: {},
    productivity: {
        required: helpers.withMessage(REQUIRED_MESSAGE, required),
        minValue: helpers.withMessage(`Минимальная значение производительности - ${MIN_PRODUCTIVITY} м.п./час`, minValue(MIN_PRODUCTIVITY)),
    },
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

// __ Оборачиваем в объект
const v$ = useVuelidate(rules, verify)


// __ Формируем данные для отображения статуса
const checkboxDataStatus: ICheckboxData = {
    name: 'status',
    data: [
        {id: 1, name: 'Активный', checked: fabricPicture.active},
        {id: 2, name: 'Архив', checked: !fabricPicture.active},
    ]
}


// __ Переменные состояния СМ
const mainMachine: IMachineState = reactive({
    machineId: fabricPicture.fabricMainMachineId,
    schemaId: fabricPicture.fabricMainMachineSchemaId,
    visible: true,
    machineLabel: 'Основная СМ',
    schemaLabel: 'Схема игл основной СМ',
    type: 'success',
})
const altMachine_1: IMachineState = reactive({
    machineId: fabricPicture.fabricAltMachineId_1,
    schemaId: fabricPicture.fabricAltMachineSchemaId_1,
    visible: true,
    machineLabel: 'Альтернативная СМ 1',
    schemaLabel: 'Схема игл альтернативной СМ 1',
    type: 'indigo',
})
const altMachine_2: IMachineState = reactive({
    machineId: fabricPicture.fabricAltMachineId_2,
    schemaId: fabricPicture.fabricAltMachineSchemaId_2,
    visible: true,
    machineLabel: 'Альтернативная СМ 2',
    schemaLabel: 'Схема игл альтернативной СМ 2',
    type: 'warning',
})


// __ Формируем список СМ для рендера
const machinesRenderList = ref([mainMachine, altMachine_1, altMachine_2])


// __ Формируем селекты для СМ
const getMachineSelect = (inMachine: IMachineState, order = 0 /* 0 - основная СМ, 1 - альт СМ 1, 2 - альт СМ 2 */) => {

    let data = machines.value
        .filter(machine => machine.active)
        .sort((a, b) => a.id - b.id)

    if (order === 0) {
        // Убираем нулевые данные для основной СМ
        data = data.filter(machine => machine.id)
    } else if (order === 1) {
        // Убираем дубликат основной СМ для альтернативной СМ 1
        data = data.filter(machine => machine.id !== mainMachine.machineId)
    } else if (order === 2) {
        // Убираем дубликат основной СМ и альтернативной СМ 1 для альтернативной СМ 2
        data = data.filter(machine => machine.id !== mainMachine.machineId && machine.id !== altMachine_1.machineId)
    }

    const selectData: ISelectDataItem[] = data.map(machine => {
        return {
            id: machine.id,
            name: machine.short_name,
            selected: machine.id === inMachine.machineId,
            disabled: false
        }
    })

    return {
        name: 'machine',
        data: selectData
    } as ISelectData
}


// __ Делаем селект для схем игл
const getMachineSchemaSelect = (machine: IMachineState, order = 0 /* 0 - основная СМ, 1 - альт СМ 1, 2 - альт СМ 2 */) => {
    let data = schemas.value
        .sort((a, b) => a.id - b.id)
        .map(schema => {
            return {
                id: schema.id,
                name: schema.schema_name,
                selected: schema.id === machine.schemaId,
                disabled: false
            }
        })

    if (order === 0) data = data.filter(schema => schema.id)    // Убираем нулевые данные для основной СМ

    return {
        name: 'machineSchema',
        data
    } as ISelectData
}


// __ Показываем/скрываем СМ
const handleMachineVisibility = (changeMainMachineFlag = false /*Флаг того, что был изменен выбор основной СМ*/) => {

    // console.log('changeMainMachineFlag: ', changeMainMachineFlag)
    // console.log('machinesRenderList: ', machinesRenderList.value)

    // Обрабатываем изменение основной СМ, сбрасываем все остальные
    if (changeMainMachineFlag) {
        machinesRenderList.value[1].machineId = 0
        machinesRenderList.value[2].machineId = 0
        // machinesRenderList.value[1].schemaId = 0
        // machinesRenderList.value[2].schemaId = 0
    }

    // Если выбор СМ - "Нет данных", сбрасываем схему игл
    if (machinesRenderList.value[1].machineId === 0) machinesRenderList.value[1].schemaId = 0
    if (machinesRenderList.value[2].machineId === 0) machinesRenderList.value[2].schemaId = 0


    // Если что-то не так с основной СМ, то скрываем все остальные
    if (machinesRenderList.value[0].machineId === 0 || machinesRenderList.value[0].schemaId === 0) {
        machinesRenderList.value[1].visible = false
        machinesRenderList.value[2].visible = false
        return
    }

    if (machinesRenderList.value[1].machineId === 0 || machinesRenderList.value[1].schemaId === 0) {
        machinesRenderList.value[2].visible = false
        return
    }

    machinesRenderList.value[0].visible = true
    machinesRenderList.value[1].visible = true
    machinesRenderList.value[2].visible = true
}


// __ Меняем СМ
const handleMachine = (event: ISelectDataItem, machineOrder: number /* 0 - основная СМ, 1 - альт СМ 1, 2 - альт СМ 2 */) => {
    console.log(event)
    let changeMainMachineFlag = false   // Флаг того, что был изменен выбор основной СМ
    if (machineOrder === 0) changeMainMachineFlag = machinesRenderList.value[machineOrder].machineId !== event.id
    machinesRenderList.value[machineOrder].machineId = event.id
    handleMachineVisibility(changeMainMachineFlag)
}


// __ Меняем схему игл
const handleMachineSchema = (event: ISelectDataItem, machineOrder: number /* 0 - основная СМ, 1 - альт СМ 1, 2 - альт СМ 2 */) => {
    machinesRenderList.value[machineOrder].schemaId = event.id
    // console.log(event)
}


// __ Меняем статус
const checkedHandlerStatus = (obj: { id: number }) => {
    console.log(obj)
    fabricPicture.active = obj.id === 1
}


// __ Обработка ввода имени рисунка ПС
const inputNameHandler = (event: InputEvent) => {
    const target = event.target as HTMLInputElement     // Утверждаем, что event.target является HTMLInputElement
    name.value = target.value.toUpperCase()             // Теперь TypeScript знает, что target имеет свойство 'value'
    // console.log(name.value)
}

// __ Показываем/скрываем поле ввода челноков для Корейца
const handleShuttleAmount = () => machinesRenderList.value.some(machine => machine.machineId === FABRIC_MACHINES.KOREAN.ID)

// __ Переменная для отображения/скрытия поля ввода челноков для Корейца
const showShuttleAmountField = ref(handleShuttleAmount())


// __ Асинхронная модальное окно
const appModalAsync = ref<any>(null)         // Получаем ссылку на модальное окно
const modalText = ref<string[]>([])
const modalType = ref('danger')

// __ Callout для вывода ошибок и предупреждений
const calloutType = ref('danger')
const calloutText = ref('')
const calloutShow = ref(false)
const calloutClose = (delay = 5000) => setTimeout(() => calloutShow.value = false, delay) // закрываем callout


// __ Отправляем форму на сервер
const formSubmit = async () => {

    // v$.value.$touch()                                // валидируем всю форму (обновляет маркеры валидации внутри объекта v$)
    const isFormCorrect = await v$.value.$validate()    // валидируем всю форму
    if (!isFormCorrect) return                          // это показатель ошибки

    // console.log('machinesRenderList: ', machinesRenderList.value)

    // __ Проверяем, заполнены ли схемы игл
    if ((altMachine_1.machineId !== 0 && altMachine_1.schemaId === 0) ||
        (altMachine_2.machineId !== 0 && altMachine_2.schemaId === 0)) {
        modalText.value = ['Заполните схему игл для всех СМ.']
        /*const result =*/ await appModalAsync.value.show()  // показываем модалку и ждем ответ
        return
    }

    // __ Проверяем, заполнены ли челноки на Корейце
    if (showShuttleAmountField.value && !shuttleAmount.value) {
        modalText.value = ['В списке стегальных машин присутствует "Кореец".', 'Укажите количество челноков для Корейца.']
        /*const result =*/ await appModalAsync.value.show()  // показываем модалку и ждем ответ
        return
    }

    // __ Формируем массив для сохранения
    // fabricPicture.id = id.value
    // fabricPicture.active = active.value

    fabricPicture.name = name.value
    fabricPicture.stitch_length = stitchLength.value
    fabricPicture.stitch_speed = stitchSpeed.value
    fabricPicture.moment_speed = momentSpeed.value
    fabricPicture.shuttle_amount = showShuttleAmountField.value ? shuttleAmount.value : 0
    fabricPicture.productivity = productivity.value
    fabricPicture.description = description.value

    fabricPicture.fabricMainMachineId = mainMachine.machineId
    fabricPicture.fabricAltMachineId_1 = altMachine_1.machineId
    fabricPicture.fabricAltMachineId_2 = altMachine_2.machineId
    fabricPicture.fabricAltMachineId_3 = 0
    fabricPicture.fabricMainMachineSchemaId = mainMachine.schemaId
    fabricPicture.fabricAltMachineSchemaId_1 = altMachine_1.schemaId
    fabricPicture.fabricAltMachineSchemaId_2 = altMachine_2.schemaId
    fabricPicture.fabricAltMachineSchemaId_3 = 0

    console.log('fabricPicture: ', fabricPicture)

    let res
    if (editMode) {
        console.log('update')
        res = await fabricStore.updateFabricPicture(fabricPicture)
    } else {
        console.log('create')
        res = await fabricStore.createFabricPicture(fabricPicture)
    }

    if (res === 'success') {
        calloutType.value = 'success'
        calloutText.value = 'Сохранено успешно'
    } else {
        calloutType.value = 'danger'
        calloutText.value = 'Упс, что-то пошло не так...'
    }

    calloutShow.value = true
    calloutClose()

    // console.log('res', res)

    // Пр-во попросило отключить автоматический переход
    // await router.push({name: 'manufacture.cell.fabrics.show'})      // переходим к списку ПС
}


watch(
    [
        () => mainMachine,
        () => altMachine_1,
        () => altMachine_2,
    ], ([
            // newMainMachine,
            // newAltMachine_1,
            // newAltMachine_2,
        ]) => {

        handleMachineVisibility()
        showShuttleAmountField.value = handleShuttleAmount()

    }, {deep: true, immediate: true})


onMounted(async () => {
    isLoading.value = true

    await getMachines()
    await getSchemas()
    v$.value.$touch()       // __ Запускаем сразу валидацию формы

    // console.log('machines: ', machines.value)
    // console.log('schemas: ', schemas.value)

    isLoading.value = false
})

</script>

<style scoped>

</style>
