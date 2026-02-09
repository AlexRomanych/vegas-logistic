<template>
    <div class="ml-2 mt-2">
        <div class="sticky top-0 pt-1 pb-1 bg-blue-200 border-2 rounded-lg border-blue-700 p-1 mb-1 max-w-fit">
            <div class="flex">
                <div>
                    <!-- attract: Код по 1С -->
                    <AppLabelMultiLine
                        :text="['Код в 1С', '']"
                        align="center"
                        text-size="small"
                        type="primary"
                        width="w-[100px]"
                    />

                    <!-- attract: Фильтр: Код 1С -->
                    <AppInputText
                        id="contract-search"
                        v-model.trim="codeFilter"
                        placeholder="Код 1С"
                        text-size="mini"
                        type="primary"
                        width="w-[100px]"
                    />
                </div>

                <div>
                    <!-- attract: ПС -->
                    <AppLabelMultiLine
                        :text="['Название', 'полотна стеганного']"
                        align="center"
                        text-size="small"
                        type="primary"
                        width="w-[310px]"
                    />

                    <!-- attract: Фильтр: название ПС -->
                    <AppInputText
                        id="name-search"
                        v-model.trim="nameFilter"
                        placeholder="Название ПС"
                        text-size="mini"
                        type="primary"
                        width="w-[310px]"
                    />
                </div>

                <div>
                    <!-- attract: Рисунок -->
                    <AppLabelMultiLine
                        :text="['Рис.', '']"
                        align="center"
                        text-size="small"
                        type="primary"
                        width="w-[50px]"
                    />

                    <!-- attract: Фильтр: Рисунок -->
                    <AppInputText
                        id="name-search"
                        v-model.trim="pictureFilter"
                        placeholder="Рис"
                        text-size="mini"
                        type="primary"
                        width="w-[50px]"
                    />
                </div>

                <div>
                    <!-- attract: Ткань -->
                    <AppLabelMultiLine
                        :text="['Ткань', '']"
                        align="center"
                        text-size="small"
                        type="primary"
                        width="w-[90px]"
                    />

                    <!-- attract: Фильтр: Ткань -->
                    <AppInputText
                        id="name-search"
                        v-model.trim="textileFilter"
                        placeholder="Ткань"
                        text-size="mini"
                        type="primary"
                        width="w-[90px]"
                    />
                </div>

                <!-- attract: Буфер -->
                <AppLabelMultiLine
                    :text="['Буфер', 'м.п.']"
                    align="center"
                    text-size="small"
                    type="primary"
                    width="w-[60px]"
                />

                <!-- attract: Минимальный запас -->
                <AppLabelMultiLine
                    :text="['Мин', 'м.п.']"
                    align="center"
                    text-size="small"
                    title="Минимальный запас"
                    type="primary"
                    width="w-[60px]"
                />

                <!-- attract: Максимальный запас -->
                <AppLabelMultiLine
                    :text="['Макс', 'м.п.']"
                    align="center"
                    text-size="small"
                    title="Максимальный запас"
                    type="primary"
                    width="w-[60px]"
                />

                <!-- attract: Средняя длина рулона -->
                <AppLabelMultiLine
                    :text="['Ср. дл.', 'м.п.']"
                    align="center"
                    text-size="small"
                    title="Средняя длина рулона"
                    type="primary"
                    width="w-[60px]"
                />

                <!-- __ Средняя длина рулона из статистики -->
                <AppLabelMultiLine
                    :text="['Ср. дл.', 'стат.']"
                    align="center"
                    text-size="small"
                    title="Средняя длина рулона"
                    type="primary"
                    width="w-[60px]"
                />

                <!-- attract: Оптимальная партия для запуска -->
                <AppLabelMultiLine
                    :text="['ОПЗ', 'м.п.']"
                    align="center"
                    text-size="small"
                    title="Оптимальная партия для запуска"
                    type="primary"
                    width="w-[60px]"
                />

                <!-- attract: Производительность -->
                <AppLabelMultiLine
                    :text="productivitySort === null ? ['Пр-сть', 'м.п./ч.'] : productivitySort ? ['Пр-сть', 'м.п./ч. ▲'] : ['Пр-сть', 'м.п./ч. ▼']"
                    align="center"
                    text-size="small"
                    title="Производительность"
                    type="primary"
                    width="w-[70px]"
                    @click="changeProductivitySort"
                />

                <div>
                    <!-- attract: Основная СМ -->
                    <AppLabelMultiLine
                        :text="['Основная', 'СМ']"
                        align="center"
                        text-size="small"
                        title="Основная стегальная машина"
                        type="success"
                        width="w-[80px]"
                    />

                    <!-- Верстка  -->
                    <div class="mt-1"></div>

                    <!-- attract: Фильтр: Основная СМ -->
                    <AppSelectSimple
                        :select-data="mainMachine"
                        text-size="mini"
                        type="success"
                        width="w-[80px]"
                        @change="filterByMachine($event, 0)"
                    />
                </div>

                <div>
                    <!-- attract: Альтернативная СМ 1 -->
                    <AppLabelMultiLine
                        :text="['Альтернат.', 'СМ 1']"
                        align="center"
                        text-size="small"
                        title="Альтернативная стегальная машина 1"
                        type="indigo"
                        width="w-[80px]"
                    />

                    <!-- Верстка  -->
                    <div class="mt-1"></div>

                    <!-- attract: Фильтр: Альтернативная СМ 1 -->
                    <AppSelectSimple
                        :select-data="mainMachine"
                        text-size="mini"
                        type="indigo"
                        width="w-[80px]"
                        @change="filterByMachine($event, 1)"
                    />
                </div>

                <div>
                    <!-- attract: Альтернативная СМ 2 -->
                    <AppLabelMultiLine
                        :text="['Альтернат.', 'СМ 2']"
                        align="center"
                        text-size="small"
                        title="Альтернативная стегальная машина 2"
                        type="warning"
                        width="w-[80px]"
                    />

                    <!-- Верстка  -->
                    <div class="mt-1"></div>

                    <!-- attract: Фильтр: Альтернативная СМ 2 -->
                    <AppSelectSimple
                        :select-data="mainMachine"
                        text-size="mini"
                        type="warning"
                        width="w-[80px]"
                        @change="filterByMachine($event, 2)"
                    />
                </div>

                <div>
                    <!-- attract: Статус -->
                    <AppLabelMultiLine
                        :text="['Статус', '']"
                        align="center"
                        text-size="small"
                        tile="Статус"
                        type="primary"
                        width="w-[80px]"
                    />

                    <!-- Верстка  -->
                    <div class="mt-1"></div>

                    <!-- attract: Фильтр: Статус -->
                    <AppSelectSimple
                        :select-data="statusSelectData"
                        text-size="mini"
                        type="primary"
                        width="w-[80px]"
                        @change="filterByStatus"
                    />
                </div>

                <!-- attract: Коэффициент перевода ткани в ПС -->
                <AppLabelMultiLine
                    :text="['Коэфф.', 'ткани']"
                    align="center"
                    text-size="small"
                    title="Коэффициент перевода ткани в ПС"
                    type="primary"
                    width="w-[60px]"
                />

                <!-- __ Количество рулонов в ПС -->
                <AppLabelMultiLine
                    :text="['Кол-во', 'рулонов']"
                    align="center"
                    text-size="small"
                    title="Количество рулонов в ПС"
                    type="primary"
                    width="w-[60px]"
                />

                <!-- attract: Описание -->
                <AppLabelMultiLine
                    :text="['Описание', '']"
                    align="center"
                    text-size="small"
                    type="primary"
                    width="w-[200px]"
                    ешеду="Описание"
                />

                <!-- attract: Кнопка создать -->
                <router-link :to="{ name: 'manufacture.cell.fabrics.create' }">
                    <AppLabelMultiLine
                        :text="['Создать', '']"
                        align="center"
                        class="cursor-pointer"
                        text-size="small"
                        type="success"
                        width="w-[70px]"
                    />
                </router-link>

                <!-- attract: Кнопка Обновить буфер -->
                <AppLabelMultiLine
                    :text="['Обновить', 'буфер']"
                    align="center"
                    class="cursor-pointer"
                    text-size="small"
                    type="danger"
                    width="w-[70px]"
                    @click="updateBuffer"
                />
            </div>
        </div>

        <!-- attract: Сами данные -->
        <div v-if="fabrics.length">
            <div class="pt-1 pb-1 bg-slate-200 border-2 rounded-lg border-slate-700 p-1 mb-1 max-w-fit">
                <div
                    v-for="fabric in fabrics"
                    :key="fabric.id"
                >
                    <div class="flex">

                        <!-- __ id -->
                        <!--<AppLabel-->
                        <!--    :text="fabric.id.toString()"-->
                        <!--    align="center"-->
                        <!--    text-size="mini"-->
                        <!--    width="w-[100px]"-->
                        <!--/>-->

                        <AppLabel
                            :text="fabric.code_1C"
                            align="center"
                            text-size="mini"
                            width="w-[100px]"
                        />

                        <AppLabel
                            :text="fabric.display_name"
                            text-size="mini"
                            width="w-[310px]"
                        />

                        <AppLabel
                            :text="fabric.picture.name"
                            align="center"
                            text-size="mini"
                            width="w-[50px]"
                        />

                        <AppLabel
                            :text="fabric.textile"
                            align="center"
                            text-size="mini"
                            width="w-[90px]"
                        />

                        <AppLabel
                            :text="fabric.buffer.amount.toFixed(2)"
                            :type="!(!round(fabric.buffer.amount, 0) && fabric.active) ? 'dark' : 'danger'"
                            align="center"
                            text-size="mini"
                            width="w-[60px]"
                        />

                        <AppLabel
                            :text="fabric.buffer.min.toFixed(2)"
                            align="center"
                            text-size="mini"
                            width="w-[60px]"
                        />

                        <AppLabel
                            :text="fabric.buffer.max.toFixed(2)"
                            align="center"
                            text-size="mini"
                            width="w-[60px]"
                        />

                        <AppLabel
                            :text="fabric.buffer.average_length.toFixed(3)"
                            :type="!(!fabric.buffer.average_length && fabric.active) ? 'dark' : 'danger'"
                            align="center"
                            text-size="mini"
                            width="w-[60px]"
                        />

                        <AppLabel
                            :text="fabric.statistic ? '✓' : '✗'"
                            :type="fabric.statistic ? 'success' : 'dark'"
                            align="center"
                            text-size="mini"
                            width="w-[60px]"
                        />

                        <AppLabel
                            :text="fabric.buffer.optimal_party.toFixed(2)"
                            align="center"
                            text-size="mini"
                            width="w-[60px]"
                        />

                        <AppLabel
                            :text="fabric.buffer.productivity.toFixed(3)"
                            :type="!(!fabric.buffer.productivity && fabric.active) ? 'dark' : 'danger'"
                            align="center"
                            text-size="mini"
                            width="w-[70px]"
                        />

                        <AppLabel
                            :text="fabric.machines[0].short_name"
                            :type="!(!fabric.machines[0].short_name && fabric.active) ? 'success' : 'danger'"
                            align="center"
                            text-size="mini"
                            width="w-[80px]"
                        />

                        <AppLabel
                            :text="fabric.machines[1].id ? fabric.machines[1].short_name : ''"
                            :type="fabric.machines[1].id ? 'indigo' : 'dark'"
                            align="center"
                            text-size="mini"
                            width="w-[80px]"
                        />

                        <AppLabel
                            :text="fabric.machines[2].id ? fabric.machines[2].short_name : ''"
                            :type="fabric.machines[2].id ? 'warning' : 'dark'"
                            align="center"
                            text-size="mini"
                            width="w-[80px]"
                        />

                        <AppLabel
                            :text="fabric.active ? 'Активный' : 'Архив'"
                            :type="fabric.active ? 'success' : 'stone'"
                            align="center"
                            text-size="mini"
                            width="w-[80px]"
                        />

                        <AppLabel
                            :text="fabric.buffer.rate.toFixed(3)"
                            :type="!(!fabric.buffer.rate && fabric.active) ? 'dark' : 'danger'"
                            align="center"
                            text-size="mini"
                            width="w-[60px]"
                        />

                        <AppLabel
                            :text="fabric.textile_layers_amount.toString()"
                            :type="fabric.textile_layers_amount === 1 ? 'success' : 'primary'"
                            align="center"
                            text-size="mini"
                            width="w-[60px]"
                        />

                        <AppLabel
                            :text="fabric.text.description ? fabric.text.description : '...'"
                            class="truncate"
                            text-size="mini"
                            width="w-[200px]"
                        />

                        <router-link :to="{ name: 'manufacture.cell.fabric.edit', params: { id: fabric.id } }">
                            <AppLabel
                                align="center"
                                class="cursor-pointer"
                                text="Редактировать"
                                text-size="mini"
                                type="warning"
                                width="w-[144px]"
                            />
                        </router-link>

                        <!--                        <AppLabel-->
                        <!--                            align="center"-->
                        <!--                            class="cursor-pointer font-bold"-->
                        <!--                            text="X"-->
                        <!--                            text-size="small"-->
                        <!--                            type="danger"-->
                        <!--                            width="w-[40px]"-->
                        <!--                            @click="fabricDelete(fabric.id)"-->
                        <!--                        />-->
                    </div>
                </div>
            </div>
        </div>

        <div v-else>
            <AppLabel
                text="Нет данных"
                type="info"
            />
        </div>
    </div>

    <AppModal
        :show="modalShow"
        mode="confirm"
        text="ПС будет удалено. Продолжить?"
        type="danger"
        @closeModal="closeModal"
    />

    <AppCallout
        v-if="calloutShow"
        :text="calloutText"
        :type="calloutType"
    />
</template>

<script setup>
import { ref, watch } from 'vue'

import { useFabricsStore } from '@/stores/FabricsStore.js'

import { FABRIC_MACHINES } from '@/app/constants/fabrics.js'

import { round } from '@/app/helpers/helpers_lib.js'

import AppLabel from '@/components/ui/labels/AppLabel.vue'
import AppModal from '@/components/ui/modals/AppModal.vue'
import AppCallout from '@/components/ui/callouts/AppCallout.vue'
import AppLabelMultiLine from '@/components/ui/labels/AppLabelMultiLine.vue'
import AppInputText from '@/components/ui/inputs/AppInputText.vue'
import AppSelectSimple from '@/components/ui/selects/AppSelectSimple.vue'

const fabricsStore = useFabricsStore()

const getAllFabrics = async () => await fabricsStore.getFabrics()
const allFabrics    = ref(await getAllFabrics())

const getFabrics = () => {
    const tempFabrics = ref(allFabrics.value)
    tempFabrics.value.sort((fabric1, fabric2) =>
        fabric1.machines[0].short_name.localeCompare(fabric2.machines[0].short_name)
    )
    tempFabrics.value.sort((fabric) => fabric.active, true)
    return tempFabrics.value
}
const fabrics    = ref(getFabrics())

console.log('allFabrics: ', allFabrics.value)

// let fabrics = await fabricsStore.getFabrics()

// attract: Сортируем по основной стегальной машине
fabrics.value.sort((fabric1, fabric2) =>
    fabric1.machines[0].short_name.localeCompare(fabric2.machines[0].short_name)
)
fabrics.value.sort((fabric) => fabric.active, true)

// union.sort((a, b) => a.element.model.localeCompare(b.element.model))

// attract: Переменные для фильтрации
const codeFilter         = ref('')
const pictureFilter      = ref('')
const nameFilter         = ref('')
const textileFilter      = ref('')
const statusFilter       = ref(1)
const mainMachineFilter  = ref(0)
const altMachineFilter_1 = ref(0)
const altMachineFilter_2 = ref(0)


// __ Сортировка
const productivitySort = ref(null)

const changeProductivitySort = () => {
    if (productivitySort.value === null) {
        productivitySort.value = true
    } else if (productivitySort.value === true) {
        productivitySort.value = false
    } else {
        productivitySort.value = null
    }
}



// attract: Селект для статуса
const statusSelectData = {
    name: 'status',
    data: [
        { id: 1, name: 'Все', selected: true, disabled: false },
        { id: 2, name: 'Активный', selected: true, disabled: false },
        { id: 3, name: 'Архив', selected: true, disabled: false },
    ],
}

// attract: Селекты для фильтрации СМ
const machinesData = [
    { id: 0, name: 'Все', selected: true, disabled: false },
    { id: -1, name: 'Любая ШМ', selected: false, disabled: false },
    { id: FABRIC_MACHINES.AMERICAN.ID, name: FABRIC_MACHINES.AMERICAN.NAME, selected: false, disabled: false },
    { id: FABRIC_MACHINES.GERMAN.ID, name: FABRIC_MACHINES.GERMAN.NAME, selected: false, disabled: false },
    { id: FABRIC_MACHINES.CHINA.ID, name: FABRIC_MACHINES.CHINA.NAME, selected: false, disabled: false },
    { id: FABRIC_MACHINES.KOREAN.ID, name: FABRIC_MACHINES.KOREAN.NAME, selected: false, disabled: false },
]

const mainMachine  = { name: 'mainMachine', data: machinesData }
// const altMachine_1 = { name: 'altMachine_1', data: machinesData }
// const altMachine_2 = { name: 'altMachine_2', data: machinesData }

console.log('fabrics', fabrics.value)

const modalShow   = ref(false)
const modalAnswer = ref(false)
const calloutShow = ref(false)
const calloutText = ref('')
const calloutType = ref('')

let deleteFabricId = 0
let fabric         = ref(null)

// Удаляем работника
const fabricDelete = (id) => {
    modalShow.value = true
    deleteFabricId  = id
    // console.log(id)
    // console.log(modalShow.value)
}

// Удаляем рабочего после модального окна
const closeModal = async (answer) => {
    modalAnswer.value = answer
    modalShow.value   = false
    // console.log('answer', modalAnswer.value)

    if (answer) {
        const fabric  = fabrics.value.find((fabric) => fabric.id === deleteFabricId)
        fabrics.value = fabrics.value.filter((fabric) => fabric.id !== deleteFabricId)
        const result  = await fabricsStore.deleteFabric(deleteFabricId)

        if (result === 'success') {
            calloutType.value = 'success'
            calloutText.value = `${fabric.name} успешно удалено`
        } else {
            calloutType.value = 'danger'
            calloutText.value = 'Упс... Что-то пошло не так...'
        }
        calloutShow.value = true
    }
}

// attract: Меняем статус
const filterByStatus = (status) => {
    statusFilter.value = status.id
}

// attract: Меняем СМ
const filterByMachine = (event, machineOrder /* 0 - основная СМ, 1 - альт СМ 1, 2 - альт СМ 2 */) => {
    switch (machineOrder) {
        case 0:
            mainMachineFilter.value = event.id
            break
        case 1:
            altMachineFilter_1.value = event.id
            break
        case 2:
            altMachineFilter_2.value = event.id
            break
    }

    // console.log('select: ', event)
    // console.log('machineOrder: ', machineOrder)
}

// attract: Обновляем буфер в соответствии с рулонами (синхронизируем с количеством рулонов)
const updateBuffer = async () => {
    await fabricsStore.updateFabricsBuffer()
    allFabrics.value = await getAllFabrics()
    fabrics.value    = getFabrics()
}

// attract: Реализация фильтра
watch(
    [
        () => codeFilter.value,
        () => nameFilter.value,
        () => pictureFilter.value,
        () => textileFilter.value,
        () => statusFilter.value,
        () => mainMachineFilter.value,
        () => altMachineFilter_1.value,
        () => altMachineFilter_2.value,
        () => productivitySort.value,
    ],
    ([
         newCodeFilter,
         newNameFilter,
         newPictureFilter,
         newTextileFilter,
         newStatusFilter,
         newMainMachineFilter,
         newAltMachineFilter_1,
         newAltMachineFilter_2,
         newProductivitySort,
     ]) => {
        fabrics.value = allFabrics.value
            .filter((fabric) => fabric.code_1C.includes(newCodeFilter.toLowerCase()))
            .filter((fabric) => fabric.display_name.toLowerCase().includes(newNameFilter.toLowerCase()))
            .filter((fabric) => fabric.textile.toLowerCase().includes(newTextileFilter.toLowerCase()))
            .filter((fabric) => fabric.picture.name.toLowerCase().includes(newPictureFilter.toLowerCase()))

        // __ Фильтр по статусу
        if (newStatusFilter === 2 || newStatusFilter === 3) {
            newStatusFilter === 2
                ? (fabrics.value = fabrics.value.filter((fabric) => fabric.active))
                : (fabrics.value = fabrics.value.filter((fabric) => !fabric.active))
        }

        // __ Фильтр по основной СМ
        if (newMainMachineFilter !== 0) {
            if (newMainMachineFilter !== -1) {
                fabrics.value = fabrics.value.filter((fabric) => fabric.machines[0].id === newMainMachineFilter)
            } else {
                fabrics.value = fabrics.value.filter((fabric) => fabric.machines[0].id)
            }
        }

        // __ Фильтр по альт. СМ 1
        if (newAltMachineFilter_1 !== 0) {
            if (newAltMachineFilter_1 !== -1) {
                fabrics.value = fabrics.value.filter((fabric) => fabric.machines[1].id === newAltMachineFilter_1)
            } else {
                fabrics.value = fabrics.value.filter((fabric) => fabric.machines[1].id)
            }
        }

        // __ Фильтр по альт. СМ 2
        if (newAltMachineFilter_2 !== 0) {
            if (newAltMachineFilter_2 !== -1) {
                fabrics.value = fabrics.value.filter((fabric) => fabric.machines[2].id === newAltMachineFilter_2)
            } else {
                fabrics.value = fabrics.value.filter((fabric) => fabric.machines[2].id)
            }
        }


        if (newProductivitySort === null) {
            fabrics.value.sort((fabric1, fabric2) =>
                fabric1.machines[0].short_name.localeCompare(fabric2.machines[0].short_name)
            )
            fabrics.value.sort((fabric) => fabric.active, true)
        } else if (newProductivitySort === true) {
            fabrics.value.sort((a, b) => a.buffer.productivity - b.buffer.productivity)
        } else {
            fabrics.value.sort((a, b) => b.buffer.productivity - a.buffer.productivity)
        }

    },
    { deep: true }
)
</script>

<style scoped>
.sticky-header {
    position: sticky;
    /* top: var(--header-height); top: 0; */
    /* padding-top: 10px; */
}
</style>
