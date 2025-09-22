<template>
    <div v-if="!isLoading" class="m-4">

        <!-- __ Выводим Табы и закрепляем их сверху -->
        <div class="sticky top-0.5 mt-1 pt-t w-full bg-slate-100">

            <div class="flex items-center">
                <div v-for="tab in tabs" :key="tab.machine.ID" class="ml-1">
                    <div :class="{'p-0.5 bg-blue-200 border-2 rounded-lg border-blue-700': tab.active}">
                        <AppLabelMultiLineTS
                            :text="[tab.machine.NAME, tab.machine.ORIGIN]"
                            :type="tab.machine.TYPE"
                            align="center"
                            height="h-[30px]"
                            text-size="small"
                            width="w-[150px]"
                            @click="setActiveTab(tab)"
                        />
                    </div>
                </div>

            </div>

            <TheDividerLine/>
        </div>

        <!-- __ Выводим время переналадки (верхняя строка) -->
        <div class="flex ml-[64px] mb-[5px]">
            <div v-for="time of renderTimes" :key="time.id">
                <div v-if="time.id !== 0">

                    <AppLabel
                        :text="time.name"
                        align="center"
                        height="h-[25px]"
                        text-size="mini"
                        type="primary"
                        width="w-[50px]"
                    />

                </div>
            </div>
        </div>

        <!-- __ Левый столбец + данные -->
        <div>
            <div v-for="(time, index) of renderTimes" :key="time.id">

                <!-- __ Левый столбец -->
                <div class="flex">
                    <div class="mr-[12px]">
                        <AppLabel
                            :text="time.name"
                            align="center"
                            height="h-[25px]"
                            text-size="mini"
                            type="primary"
                            width="w-[50px]"
                        />
                    </div>

                    <!--__ Данные -->
                    <div v-for="(subTime, subIndex) of time.pictures" :key="subTime.pic.id" class="mt-[3px]">

                        <AppInputNumberSimpleTS
                            :id="'t' + index.toString() + 's' + subIndex.toString()"
                            v-model:inputNumber.number="subTime.tuning_time"
                            :bg-color="true"
                            :disabled="time.id === subTime.pic.id"
                            :show-spins="false"
                            :show-zeros="showZeros === null ? subTime.db : showZeros"
                            :type="getType(time, subTime)"
                            align="center"
                            class="ml-[-1px] mt-[-1px]"
                            height="h-[40px]"
                            placeholder=""
                            text-size="micro"
                            width="w-[51px]"
                            @take-focus="takeFocus"
                            @leave-focus="handleTime(time, subTime)"
                            @keyup.enter="handleTime(time, subTime)"
                            @keydown.ctrl.delete="eraseTime(time, subTime)"
                        />

                    </div>

                </div>
            </div>
        </div>


    </div>
</template>

<script lang="ts" setup>

import { onMounted, reactive, ref } from 'vue'

import type { ITimeItem, ITimePictureSchema } from '@/types'

import { useFabricsStore } from '@/stores/FabricsStore.js'
import { FABRIC_MACHINES, type IConstFabricMachine, type IMachineKey } from '@/app/constants/fabrics.ts'

import { deepCopy } from '@/app/helpers/helpers_lib.ts'

import AppLabelMultiLineTS from '@/components/ui/labels/AppLabelMultiLineTS.vue'
import TheDividerLine from '@/components/dashboard/manufacture/cells/components/TheDividerLine.vue'
import AppLabel from '@/components/ui/labels/AppLabel.vue'
import AppInputNumberSimpleTS from '@/components/ui/inputs/AppInputNumberSimpleTS.vue'
// import AppInputTextTS from '@/components/ui/inputs/AppInputTextTS.vue'

// __ Loader
import { useLoading } from 'vue-loading-overlay'
import { loaderHandler } from '@/app/helpers/helpers.ts'


const isLoading = ref(true)

// __ End Loader


interface ITab {
    shown: boolean,
    typePassive: string
    active: boolean
    machine: IConstFabricMachine
}

type ITabKey = 'common' | 'american' | 'german' | 'china' | 'korean'
type ITabs = Record<ITabKey, ITab>


const fabricsStore = useFabricsStore()

// __ Подготавливаем переменные
const tuningTimes = ref<ITimeItem[]>([])
const renderTimes = ref<ITimeItem[]>([])

const showZeros = ref<null | boolean>(null) // Показывать нули в таблице


// __ Задаем отображение вкладок СМ
const TYPE_PASSIVE = 'dark'

// __ Общая вкладка
const COMMON_TAB: IConstFabricMachine = {
    ID: 0,
    TITLE: '',
    NAME: 'Все стегальные',
    ORIGIN: 'машины',
    INDEX: 'common',
    TYPE: 'dark'
}

const tabs: ITabs = reactive({
    common: {
        shown: true,
        active: false,
        typePassive: TYPE_PASSIVE,
        machine: COMMON_TAB
    },
    american: {
        shown: true,
        active: false,
        typePassive: TYPE_PASSIVE,
        machine: FABRIC_MACHINES.AMERICAN
    },
    german: {
        shown: true,
        active: false,
        typePassive: TYPE_PASSIVE,
        machine: FABRIC_MACHINES.GERMAN
    },
    china: {
        shown: true,
        active: false,
        typePassive: TYPE_PASSIVE,
        machine: FABRIC_MACHINES.CHINA
    },
    korean: {
        shown: true,
        active: false,
        typePassive: TYPE_PASSIVE,
        machine: FABRIC_MACHINES.KOREAN
    },
    // oneNeedle: {id: FABRIC_MACHINES.AMERICAN, shown: true, name: ['Одноиголка', '']},
    // test: {id: 6, shown: true, name: ['Machine', 'Test']},
})


// __ Получаем время переналадки
const getTuningTime = async () => {
    tuningTimes.value = await fabricsStore.getFabricsPicturesTuningTime() as ITimeItem[]
    tuningTimes.value = tuningTimes.value.filter(item => item.id !== 0)

    // Дополняем матрицу пустыми объектами
    const NULL_PICTURE: ITimePictureSchema = {
        pic: {
            id: 0,
            name: '',
            active: true,
            machine: {
                id: 0,
                name: '',
                short_name: ''
            }
        },
        tuning_time: 0,
        db: false,  // __ Для отрисовки '0' в компоненте (признак, что это время из БД на сервере, а не сгенерировано на фронте)
    }

    tuningTimes.value.forEach((time, /*index*/) => {

        tuningTimes.value.forEach((subTime, /*subIndex*/) => {

            const findTime = time.pictures.find(item => item.pic.id === subTime.id)
            if (!findTime) {
                const dubNullPicture = JSON.parse(JSON.stringify(NULL_PICTURE)) as ITimePictureSchema

                dubNullPicture.pic.id = subTime.id
                dubNullPicture.pic.name = subTime.name
                dubNullPicture.pic.active = subTime.active
                dubNullPicture.pic.machine = {...subTime.machine}
                dubNullPicture.tuning_time = 0

                time.pictures.push(dubNullPicture)
            } else {
                findTime.db = true
            }
        })

        // Сортируем время по id, а потом по СМ, считая, что сортировка в javascript является стабильной
        time.pictures = time.pictures
            .sort((a, b) => a.pic.id - b.pic.id)
            .sort((a, b) => a.pic.machine.id - b.pic.machine.id)
    })

    // Делаем то же самое для всего массива
    tuningTimes.value = tuningTimes.value
        .sort((a, b) => a.id - b.id)
        .sort((a, b) => a.machine.id - b.machine.id)
}

// __ Устанавливаем активную вкладку
const setActiveTab = (tab: ITab) => {

    // Меняем активную вкладку
    let key: ITabKey
    for (key in tabs) {
        tabs[key].active = (tabs[key] === tab)
    }

    // Обновляем время в зависимости от выбранной вкладки
    renderTimes.value = deepCopy(tuningTimes.value)

    // Если выбрана общая вкладка, то просто возвращаем исходный массив
    if (tab === tabs.common) return

    // Отфильтровываем массив по выбранной вкладке (СМ)
    renderTimes.value = renderTimes.value.filter(item => item.machine.id === tab.machine.ID)
    renderTimes.value.forEach(item => {
        item.pictures = item.pictures.filter(picture => picture.pic.machine.id === tab.machine.ID)
    })

    // Сортируем время по id
    // renderTimes.value = renderTimes.value.sort((a, b) => a.id - b.id)

    // Сортируем время по алфавиту
    // renderTimes.value = renderTimes.value.sort((a, b) => a.name.localeCompare(b.name, 'ru'))

    // Сортируем время по СМ
    // renderTimes.value = renderTimes.value.sort((a, b) => a.machine.id - b.machine.id)

}


// __ Получаем время для отображения в шаблоне
const getTime = (time: ITimeItem, subTime: ITimeItem) => {
    if (time.id === subTime.id) return ''

    const findTime = time.pictures.find(item => item.pic.id === subTime.id)
    if (findTime) return findTime.tuning_time.toString()

    return ''
}

// __ Получаем разукрашку для отображения в шаблоне
const getType = (time: ITimeItem, subTime: ITimePictureSchema) => {
    if (time.id === subTime.pic.id) return 'light'

    if (time.machine.id === subTime.pic.machine.id) {
        // Утверждаем, что массив ключей является массивом IMachineKey
        const keys = Object.keys(FABRIC_MACHINES) as IMachineKey[]

        // Используем findIndex для более эффективного поиска
        const findMachineKeyIndex = keys.findIndex(key => FABRIC_MACHINES[key].ID === time.machine.id)

        if (findMachineKeyIndex !== -1) {
            const findMachineKey = keys[findMachineKeyIndex]
            return FABRIC_MACHINES[findMachineKey].TYPE
        }
    }

    return 'dark'
}


// __ Обработчик изменения времени
const handleTime = async (time: ITimeItem, subTime: ITimePictureSchema) => {
    console.log('time.id:', time.id)
    console.log('subTime.id:', subTime.pic.id)
    console.log('tuning_time:', subTime.tuning_time)

    showZeros.value = subTime.db!

    // Находим исходник, из которого все копируется
    const findTime = tuningTimes.value.find(item => item.id === time.id)
    const findSubTime = findTime?.pictures.find(item => item.pic.id === subTime.pic.id)

    // Выходим, если не заполнено время, возвращая копию из массива
    if (subTime.tuning_time === null) {
        if (findSubTime) {
            subTime.tuning_time = findSubTime.tuning_time
        }
        return
    }

    if (subTime.tuning_time === findSubTime?.tuning_time && subTime.db) return    // Выходим, если время не изменилось

    /*const res =*/ await fabricsStore.setFabricsPicturesTuningTime(time.id, subTime.pic.id, subTime.tuning_time) // Обновляем время

    if (findSubTime) {
        findSubTime.tuning_time = subTime.tuning_time   // Обновляем время в исходнике без перезагрузки
        findSubTime.db = true   // Обновляем признак, что время из БД на сервере, а не сгенерировано на фронте
    }

}


// __ Обработчик изменения времени
const eraseTime = async (time: ITimeItem, subTime: ITimePictureSchema) => {
    console.log('er time.id:', time.id)
    console.log('subTime.id:', subTime.pic.id)
    console.log('tuning_time:', subTime.tuning_time)

    // Находим исходник, из которого все копируется
    const findTime = tuningTimes.value.find(item => item.id === time.id)
    if (!findTime) return

    const findSubTime = findTime.pictures.find(item => item.pic.id === subTime.pic.id)
    if (!findSubTime) return

    await fabricsStore.deleteFabricsPicturesTuningTime(time.id, subTime.pic.id)

    subTime.tuning_time = 0
    findSubTime.tuning_time = subTime.tuning_time // Обновляем время в исходнике без перезагрузки
    findSubTime.db = false // Обновляем признак, что время из БД на сервере, а не сгенерировано на фронте
}


const takeFocus = (/*time: ITimeItem, subTime: ITimePictureSchema*/) => {
    showZeros.value = true
    // console.log('takeFocus:')
}

onMounted(async () => {

    isLoading.value = true
    const loadingService = useLoading()
    await loaderHandler(
        loadingService,
        async () => {
            await getTuningTime()
            setActiveTab(tabs.common)
        },
        undefined,
        // false,
    )
    isLoading.value = false
    console.log('tuningTime:', tuningTimes.value)
})

</script>

<style scoped>

</style>
