<template>

    <div class="ml-2 mt-2">

        <!-- attract: Шапка таблицы -->
        <div class="sticky top-0 flex p-1 mb-1 bg-blue-200 border-2 rounded-lg border-blue-700 max-w-fit">
            <div>
                <!-- __ Название рисунка -->
                <AppLabelMultiLine
                    v-if="render.pictureName.show"
                    :text="render.pictureName.header"
                    :title="render.pictureName.title"
                    :type="render.pictureName.type(true)"
                    :width="render.pictureName.width"
                    align="center"
                    class="header-item"
                    text-size="mini"
                />

                <!-- __ Фильтр: Название рисунка -->
                <AppInputText
                    v-if="render.pictureName.show"
                    id="picture-name-search"
                    v-model.trim="nameFilter"
                    :width="render.pictureName.width"
                    height="h-[30px]"
                    placeholder="Рис."
                    text-size="mini"
                    type="primary"
                    @input="handlePictureNameInput"
                />
            </div>

            <div>
                <!-- __ Статус -->
                <AppLabelMultiLine
                    v-if="render.status.show"
                    :text="render.status.header"
                    :title="render.status.title"
                    :type="render.status.type(true)"
                    :width="render.status.width"
                    align="center"
                    class="header-item"
                    text-size="mini"
                />

                <!-- Верстка  -->
                <div class="mt-2"></div>

                <!-- __ Фильтр: Статус -->
                <AppSelectSimple
                    :select-data="statusSelectData"
                    :width="render.status.width"
                    height="h-[30px]"
                    text-size="mini"
                    type="primary"
                    @change="filterByStatus"
                />
            </div>

            <!-- __ Длина стежка -->
            <AppLabelMultiLine
                v-if="render.stitchLength.show"
                :text="render.stitchLength.header"
                :title="render.stitchLength.title"
                :type="render.stitchLength.type(true)"
                :width="render.stitchLength.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!-- __ Скорость стежков -->
            <AppLabelMultiLine
                v-if="render.stitchSpeed.show"
                :text="render.stitchSpeed.header"
                :title="render.stitchSpeed.title"
                :type="render.stitchSpeed.type(true)"
                :width="render.stitchSpeed.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!-- __ Мгновенная скорость -->
            <AppLabelMultiLine
                v-if="render.momentSpeed.show"
                :text="render.momentSpeed.header"
                :title="render.momentSpeed.title"
                :type="render.momentSpeed.type(true)"
                :width="render.momentSpeed.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!-- __ Кол-во челноков (для корейца) -->
            <AppLabelMultiLine
                v-if="render.shuttleAmount.show"
                :text="render.shuttleAmount.header"
                :title="render.shuttleAmount.title"
                :type="render.shuttleAmount.type(true)"
                :width="render.shuttleAmount.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <div>
                <!-- __ Основная СМ -->
                <AppLabelMultiLine
                    v-if="render.mainMachine.show"
                    :text="render.mainMachine.header"
                    :title="render.mainMachine.title"
                    :type="render.mainMachine.type(true)"
                    :width="render.mainMachine.width"
                    align="center"
                    class="header-item"
                    text-size="mini"
                />

                <!-- Верстка  -->
                <div class="mt-2"></div>

                <!-- __ Фильтр: Основная СМ -->
                <AppSelectSimple
                    :select-data="mainMachine"
                    :type="render.mainMachine.type(true)"
                    :width="render.mainMachine.width"
                    text-size="mini"
                    @change="filterByMachine($event, 0)"
                />
            </div>

            <!-- __ Схема игл основной СМ -->
            <AppLabelMultiLine
                v-if="render.mainMachineSchema.show"
                :text="render.mainMachineSchema.header"
                :title="render.mainMachineSchema.title"
                :type="render.mainMachineSchema.type(true)"
                :width="render.mainMachineSchema.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <div>
                <!-- __ Альтернативная СМ 1 -->
                <AppLabelMultiLine
                    v-if="render.fabricAltMachine_1.show"
                    :text="render.fabricAltMachine_1.header"
                    :title="render.fabricAltMachine_1.title"
                    :type="render.fabricAltMachine_1.type(true)"
                    :width="render.fabricAltMachine_1.width"
                    align="center"
                    class="header-item"
                    text-size="mini"
                />

                <!-- Верстка  -->
                <div class="mt-2"></div>

                <!-- __ Фильтр: Альтернативная СМ 1 -->
                <AppSelectSimple
                    :select-data="mainMachine"
                    :type="render.fabricAltMachine_1.type(true)"
                    :width="render.fabricAltMachine_1.width"
                    text-size="mini"
                    @change="filterByMachine($event, 1)"
                />
            </div>

            <!-- __ Схема игл Альтернативной СМ 1 -->
            <AppLabelMultiLine
                v-if="render.fabricAltMachineSchema_1.show"
                :text="render.fabricAltMachineSchema_1.header"
                :title="render.fabricAltMachineSchema_1.title"
                :type="render.fabricAltMachineSchema_1.type(true)"
                :width="render.fabricAltMachineSchema_1.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <div>
                <!-- __ Альтернативная СМ 2 -->
                <AppLabelMultiLine
                    v-if="render.fabricAltMachine_2.show"
                    :text="render.fabricAltMachine_2.header"
                    :title="render.fabricAltMachine_2.title"
                    :type="render.fabricAltMachine_2.type(true)"
                    :width="render.fabricAltMachine_2.width"
                    align="center"
                    class="header-item"
                    text-size="mini"
                />

                <!-- Верстка  -->
                <div class="mt-2"></div>

                <!-- attract: Фильтр: Альтернативная СМ 2 -->
                <AppSelectSimple
                    :select-data="mainMachine"
                    :type="render.fabricAltMachine_2.type(true)"
                    :width="render.fabricAltMachine_2.width"
                    text-size="mini"
                    @change="filterByMachine($event, 2)"
                />
            </div>

            <!-- __ Схема игл Альтернативной СМ 2 -->
            <AppLabelMultiLine
                v-if="render.fabricAltMachineSchema_2.show"
                :text="render.fabricAltMachineSchema_2.header"
                :title="render.fabricAltMachineSchema_2.title"
                :type="render.fabricAltMachineSchema_2.type(true)"
                :width="render.fabricAltMachineSchema_2.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!-- __ Описание -->
            <AppLabelMultiLine
                v-if="render.description.show"
                :text="render.description.header"
                :title="render.description.title"
                :type="render.description.type(true)"
                :width="render.description.width"
                align="center"
                class="header-item"
                text-size="mini"
            />

            <!-- __ Кнопка 'Создать' -->
            <router-link :to="{ name: 'manufacture.cell.fabrics.picture.create' }">
                <AppLabelMultiLine
                    :text="['Создать', 'рисунок']"
                    align="center"
                    class="cursor-pointer"
                    text-size="mini"
                    type="success"
                    width="w-[90px]"
                />
            </router-link>

        </div>

        <div
            v-if="fabricPictures.length"
            class="p-1 mb-1 bg-blue-100 border-2 rounded-lg border-blue-600 max-w-fit"
        >
            <div
                v-for="fabricPicture in fabricPictures"
                :key="fabricPicture.id"
                class="flex"
            >
                <!-- __ № рулона  -->
                <AppLabel
                    v-if="render.pictureName.show"
                    :text="render.pictureName.data(fabricPicture)"
                    :title="render.pictureName.title"
                    :type="render.pictureName.type(false, fabricPicture)"
                    :width="render.pictureName.width"
                    align="center"
                    text-size="micro"
                />

                <!-- __ № Статус -->
                <AppLabel
                    v-if="render.status.show"
                    :text="render.status.data(fabricPicture)"
                    :title="render.status.title"
                    :type="render.status.type(false, fabricPicture)"
                    :width="render.status.width"
                    align="center"
                    text-size="micro"
                />

                <!-- __ Длина стежка -->
                <AppLabel
                    v-if="render.stitchLength.show"
                    :text="render.stitchLength.data(fabricPicture)"
                    :title="render.stitchLength.title"
                    :type="render.stitchLength.type(false, fabricPicture)"
                    :width="render.stitchLength.width"
                    align="center"
                    text-size="micro"
                />

                <!-- __ Скорость стежков -->
                <AppLabel
                    v-if="render.stitchSpeed.show"
                    :text="render.stitchSpeed.data(fabricPicture)"
                    :title="render.stitchSpeed.title"
                    :type="render.stitchSpeed.type(false, fabricPicture)"
                    :width="render.stitchSpeed.width"
                    align="center"
                    text-size="micro"
                />

                <!-- __ Мгновенная скорость -->
                <AppLabel
                    v-if="render.momentSpeed.show"
                    :text="render.momentSpeed.data(fabricPicture)"
                    :title="render.momentSpeed.title"
                    :type="render.momentSpeed.type(false, fabricPicture)"
                    :width="render.momentSpeed.width"
                    align="center"
                    text-size="micro"
                />

                <!-- __ Кол-во челноков (для корейца) -->
                <AppLabel
                    v-if="render.shuttleAmount.show"
                    :text="render.shuttleAmount.data(fabricPicture)"
                    :title="render.shuttleAmount.title"
                    :type="render.shuttleAmount.type(false, fabricPicture)"
                    :width="render.shuttleAmount.width"
                    align="center"
                    text-size="micro"
                />

                <!-- __ Основная СМ -->
                <AppLabel
                    v-if="render.mainMachine.show"
                    :text="render.mainMachine.data(fabricPicture)"
                    :title="render.mainMachine.title"
                    :type="render.mainMachine.type(false, fabricPicture)"
                    :width="render.mainMachine.width"
                    align="center"
                    text-size="micro"
                />

                <!-- __ Схема игл основной СМ -->
                <AppLabel
                    v-if="render.mainMachineSchema.show"
                    :text="render.mainMachineSchema.data(fabricPicture)"
                    :title="render.mainMachineSchema.title"
                    :type="render.mainMachineSchema.type(false, fabricPicture)"
                    :width="render.mainMachineSchema.width"
                    align="center"
                    text-size="micro"
                />

                <!-- __ Альтернативная СМ 1 -->
                <AppLabel
                    v-if="render.fabricAltMachine_1.show"
                    :text="render.fabricAltMachine_1.data(fabricPicture)"
                    :title="render.fabricAltMachine_1.title"
                    :type="render.fabricAltMachine_1.type(false, fabricPicture)"
                    :width="render.fabricAltMachine_1.width"
                    align="center"
                    text-size="micro"
                />

                <!-- __ Схема игл Альтернативной СМ 1 -->
                <AppLabel
                    v-if="render.fabricAltMachineSchema_1.show"
                    :text="render.fabricAltMachineSchema_1.data(fabricPicture)"
                    :title="render.fabricAltMachineSchema_1.title"
                    :type="render.fabricAltMachineSchema_1.type(false, fabricPicture)"
                    :width="render.fabricAltMachineSchema_1.width"
                    align="center"
                    text-size="micro"
                />

                <!-- __ Альтернативная СМ 2 -->
                <AppLabel
                    v-if="render.fabricAltMachine_2.show"
                    :text="render.fabricAltMachine_2.data(fabricPicture)"
                    :title="render.fabricAltMachine_2.title"
                    :type="render.fabricAltMachine_2.type(false, fabricPicture)"
                    :width="render.fabricAltMachine_2.width"
                    align="center"
                    text-size="micro"
                />

                <!-- __ Схема игл Альтернативной СМ 2 -->
                <AppLabel
                    v-if="render.fabricAltMachineSchema_2.show"
                    :text="render.fabricAltMachineSchema_2.data(fabricPicture)"
                    :title="render.fabricAltMachineSchema_2.title"
                    :type="render.fabricAltMachineSchema_2.type(false, fabricPicture)"
                    :width="render.fabricAltMachineSchema_2.width"
                    align="center"
                    text-size="micro"
                />

                <!-- __ Описание -->
                <AppLabel
                    v-if="render.description.show"
                    :text="render.description.data(fabricPicture)"
                    :title="render.description.title"
                    :type="render.description.type(false, fabricPicture)"
                    :width="render.description.width"
                    align="center"
                    text-size="micro"
                />

                <!-- __ Кнопка 'Редактировать' -->
                <router-link :to="{ name: 'manufacture.cell.fabrics.picture.edit', params: { id: fabricPicture.id } }">
                    <AppLabel
                        align="center"
                        class="cursor-pointer"
                        text="Редактировать"
                        text-size="micro"
                        type="warning"
                        width="w-[90px]"
                    />
                </router-link>


            </div>

        </div>

        <div v-else>
            <div class="mt-5">

                <!-- __ Данные не найдены... -->
                <AppLabel
                    align="center"
                    height="h-[100px]"
                    text="Данные не найдены..."
                    text-size="huge"
                    type="warning"
                    width="w-[300px]"
                />
            </div>
        </div>

    </div>

</template>

<script setup>

import {reactive, ref, watch} from 'vue'

import {FABRIC_MACHINES} from '@/app/constants/fabrics.js'

// import {useFabricsStore} from '@/stores/FabricsStore.js'
import {useFabricsStore} from '@/stores/FabricsStore.js'
import AppInputText from '@/components/ui/inputs/AppInputText.vue'
import AppLabelMultiLine from '@/components/ui/labels/AppLabelMultiLine.vue'
import AppLabel from '@/components/ui/labels/AppLabel.vue'
import AppSelectSimple from '@/components/ui/selects/AppSelectSimple.vue'

const fabricsStore = useFabricsStore()

const getFabricPictures = async () => {
    const pics = await fabricsStore.getFabricPictures()
    return pics
        .filter((fabricPicture) => fabricPicture.id !== 0)
        .sort((a, b) => a.name.localeCompare(b.name))
}

const allFabricPictures = await getFabricPictures()
const fabricPictures = ref(allFabricPictures)
// console.log('fabricPictures: ', fabricPictures.value)


// attract: Задаем глобальный объект для унификации отображения рулонов
// const getTypeOfRoll = (rollStatus, flag = false) => (flag ? 'primary' : getTypeByRollStatus(rollStatus))

const render = reactive({
    pictureName: {
        header: ['Название', 'рисунка'],
        width: 'w-[80px]',
        show: true,
        title: 'Название рисунка',
        type: (flag = false, fabricPicture) => 'primary',
        // type: (flag = false, roll) => getTypeOfRoll(roll?.status, flag),
        // type: 'primary',
        data: (fabricPicture) => fabricPicture.name,
    },
    status: {
        header: ['Статус', 'рисунка'],
        width: 'w-[80px]',
        show: true,
        title: 'Статус рисунка',
        type: (flag = false, fabricPicture) => (flag ? 'primary' : fabricPicture.active ? 'primary' : 'stone'),
        data: (fabricPicture) => fabricPicture.active ? 'Активный' : 'Архив',
    },
    stitchLength: {
        header: ['Длина', 'стежка, мм'],
        width: 'w-[80px]',
        show: true,
        title: 'Длина стежка, мм',
        type: (flag = false, fabricPicture) => 'primary',
        data: (fabricPicture) => fabricPicture.stitch_length.toString(),
    },
    stitchSpeed: {
        header: ['Скорость', 'стежков, шт./мин'],
        width: 'w-[120px]',
        show: true,
        title: 'Скорость стежков, шт./мин',
        type: (flag = false, fabricPicture) => 'primary',
        data: (fabricPicture) => fabricPicture.stitch_speed.toString(),
    },
    momentSpeed: {
        header: ['Мгновенная', 'скорость, м/час'],
        width: 'w-[120px]',
        show: true,
        title: 'Мгновенная скорость, м/час',
        type: (flag = false, fabricPicture) => 'primary',
        data: (fabricPicture) => fabricPicture.moment_speed.toString(),
    },
    shuttleAmount: {
        header: ['Кол-во челноков', 'для Корейца, шт.'],
        width: 'w-[120px]',
        show: true,
        title: 'Мгновенная скорость, м/час',
        type: (flag = false, fabricPicture) => 'primary',
        data: (fabricPicture) => fabricPicture.shuttle_amount !== null ? fabricPicture.shuttle_amount.toString() : '--',
    },
    mainMachine: {
        header: ['Основная', 'СМ'],
        width: 'w-[90px]',
        show: true,
        title: 'Основная Стегальная машина',
        type: (flag = false, fabricPicture) => flag ? 'success' : fabricPicture?.machines.fabricMainMachine.machine.id ? 'success' : 'dark',
        data: (fabricPicture) => fabricPicture.machines.fabricMainMachine.machine.id ? fabricPicture.machines.fabricMainMachine.machine.short_name : '',
    },
    mainMachineSchema: {
        header: ['Схема игл', 'основной СМ'],
        width: 'w-[90px]',
        show: true,
        title: 'Схема игл основной СМ',
        type: (flag = false, fabricPicture) => flag ? 'success' : fabricPicture?.machines.fabricMainMachine.schema.id ? 'success' : 'dark',
        data: (fabricPicture) => fabricPicture.machines.fabricMainMachine.schema.id ? fabricPicture.machines.fabricMainMachine.schema.schema : '',
    },
    fabricAltMachine_1: {
        header: ['Альтернат.', 'СМ 1'],
        width: 'w-[90px]',
        show: true,
        title: 'Альтернат. СМ 1',
        type: (flag = false, fabricPicture) => flag ? 'indigo' : fabricPicture?.machines.fabricAltMachine_1.machine.id ? 'indigo' : 'dark',
        data: (fabricPicture) => fabricPicture.machines.fabricAltMachine_1.machine.id ? fabricPicture.machines.fabricAltMachine_1.machine.short_name : '',
    },
    fabricAltMachineSchema_1: {
        header: ['Схема игл', 'альтерн. СМ 1'],
        width: 'w-[90px]',
        show: true,
        title: 'Схема игл альтернативной. СМ 1',
        type: (flag = false, fabricPicture) => flag ? 'indigo' : fabricPicture?.machines.fabricAltMachine_1.schema.id ? 'indigo' : 'dark',
        data: (fabricPicture) => fabricPicture.machines.fabricAltMachine_1.schema.id ? fabricPicture.machines.fabricAltMachine_1.schema.schema : '',
    },
    fabricAltMachine_2: {
        header: ['Альтернат.', 'СМ 2'],
        width: 'w-[90px]',
        show: true,
        title: 'Альтернат. СМ 1',
        type: (flag = false, fabricPicture) => flag ? 'warning' : fabricPicture?.machines.fabricAltMachine_2.machine.id ? 'warning' : 'dark',
        data: (fabricPicture) => fabricPicture.machines.fabricAltMachine_2.machine.id ? fabricPicture.machines.fabricAltMachine_2.machine.short_name : '',
    },
    fabricAltMachineSchema_2: {
        header: ['Схема игл', 'альтерн. СМ 2'],
        width: 'w-[90px]',
        show: true,
        title: 'Схема игл альтернативной. СМ 2',
        type: (flag = false, fabricPicture) => flag ? 'warning' : fabricPicture?.machines.fabricAltMachine_2.schema.id ? 'warning' : 'dark',
        data: (fabricPicture) => fabricPicture.machines.fabricAltMachine_2.schema.id ? fabricPicture.machines.fabricAltMachine_2.schema.schema : '',
    },
    description: {
        header: ['Описание', 'рисунка'],
        width: 'w-[200px]',
        show: true,
        title: 'Описание рисунка',
        type: (flag = false, fabricPicture) => 'primary',
        data: (fabricPicture) => fabricPicture.description,

    }
})


const handlePictureNameInput = () => {
}


// __ Фильтры
const nameFilter = ref('')
const statusFilter = ref(1)
const mainMachineFilter = ref(0)
const altMachineFilter_1 = ref(0)
const altMachineFilter_2 = ref(0)

// __ Селект для статуса
const statusSelectData = {
    name: 'status',
    data: [
        {id: 1, name: 'Все', selected: true, disabled: false},
        {id: 2, name: 'Активный', selected: true, disabled: false},
        {id: 3, name: 'Архив', selected: true, disabled: false},
    ],
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

// __ Меняем статус
const filterByStatus = (status) => {
    statusFilter.value = status.id
}

// __ Меняем СМ
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
}

// __ Реализация фильтра
watch(
    [
        () => nameFilter.value,
        () => statusFilter.value,
        () => mainMachineFilter.value,
        () => altMachineFilter_1.value,
        () => altMachineFilter_2.value,
    ],
    ([
         newNameFilter,
         newStatusFilter,
         newMainMachineFilter,
         newAltMachineFilter_1,
         newAltMachineFilter_2,
     ]) => {

        fabricPictures.value = allFabricPictures
        fabricPictures.value = fabricPictures.value.filter((picture) => picture.name.toLowerCase().includes(newNameFilter.toLowerCase()))

        // Фильтр по статусу
        if (newStatusFilter === 2 || newStatusFilter === 3) {
            newStatusFilter === 2
                ? (fabricPictures.value = fabricPictures.value.filter((picture) => picture.active))
                : (fabricPictures.value = fabricPictures.value.filter((picture) => !picture.active))
        }

        // Фильтр по основной СМ
        if (newMainMachineFilter !== 0) {
            fabricPictures.value = fabricPictures.value.filter((picture) => picture.machines.fabricMainMachine.machine.id === newMainMachineFilter)
        }

        // Фильтр по альт. СМ 1
        if (newAltMachineFilter_1 !== 0) {
            fabricPictures.value = fabricPictures.value.filter((picture) => picture.machines.fabricAltMachine_1.machine.id === newAltMachineFilter_1)
        }

        // Фильтр по альт. СМ 2
        if (newAltMachineFilter_2 !== 0) {
            fabricPictures.value = fabricPictures.value.filter((picture) => picture.machines.fabricAltMachine_2.machine.id === newAltMachineFilter_2)
        }
    },
    {deep: true}
)


</script>

<style scoped>

</style>
