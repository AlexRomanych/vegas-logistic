<template>
    <div class="ml-2 mt-2">

        <!-- attract: Шапка таблицы -->
        <div class="sticky top-0 flex p-1 mb-1 bg-blue-200 border-2 rounded-lg border-blue-700 max-w-fit">

            <!-- attract: Код СМ (id) -->
            <AppLabelMultiLine
                :text="render.id.header"
                :title="render.id.title"
                :type="render.id.type(true)"
                :width="render.id.width"
                align="center"
                class="header-item"
                text-size="normal"
            />

            <!-- attract: Название СМ -->
            <AppLabelMultiLine
                :text="render.name.header"
                :title="render.name.title"
                :type="render.name.type(true)"
                :width="render.name.width"
                align="center"
                class="header-item"
                text-size="normal"
            />

            <!-- attract: Сокращенное название СМ -->
            <AppLabelMultiLine
                :text="render.shortName.header"
                :title="render.shortName.title"
                :type="render.shortName.type(true)"
                :width="render.shortName.width"
                align="center"
                class="header-item"
                text-size="normal"
            />

            <!-- attract: Статус СМ -->
            <AppLabelMultiLine
                :text="render.active.header"
                :title="render.active.title"
                :type="render.active.type(true)"
                :width="render.active.width"
                align="center"
                class="header-item"
                text-size="normal"
            />

            <!-- attract: Изменить статус СМ -->
            <AppLabelMultiLine
                :text="render.activeChange.header"
                :title="render.activeChange.title"
                :type="render.activeChange.type(true)"
                :width="render.activeChange.width"
                align="center"
                class="header-item"
                text-size="normal"
            />

            <!-- attract: Описание СМ -->
            <AppLabelMultiLine
                :text="render.description.header"
                :title="render.description.title"
                :type="render.description.type(true)"
                :width="render.description.width"
                align="center"
                class="header-item"
                text-size="normal"
            />

        </div>


        <!-- attract: Сами данные -->
        <div class="p-1 mb-1 bg-blue-100 border-2 rounded-lg border-blue-600 max-w-fit">

            <div v-for="fabricMachine in fabricsMachines" :key="fabricMachine.id" class="flex">

                <!-- attract: Код СМ (id) -->
                <AppLabel
                    :text="render.id.data(fabricMachine)"
                    :title="render.id.title"
                    :type="render.id.type()"
                    :width="render.id.width"
                    align="center"
                    text-size="mini"
                />

                <!-- attract: Название СМ -->
                <AppLabel
                    :text="render.name.data(fabricMachine)"
                    :title="render.name.title"
                    :type="render.name.type()"
                    :width="render.name.width"
                    align="center"
                    text-size="mini"
                />

                <!-- attract: Сокращенное название СМ -->
                <AppLabel
                    :text="render.shortName.data(fabricMachine)"
                    :title="render.shortName.title"
                    :type="render.shortName.type()"
                    :width="render.shortName.width"
                    align="center"
                    text-size="mini"
                />

                <!-- attract: Статус СМ -->
                <AppLabel
                    :text="render.active.data(fabricMachine)"
                    :title="render.active.title"
                    :type="render.active.type(false, fabricMachine)"
                    :width="render.active.width"
                    align="center"
                    text-size="mini"
                />

                <!-- attract: Изменить статус СМ -->
                <AppLabel
                    :text="render.activeChange.data(fabricMachine)"
                    :title="render.activeChange.title"
                    :type="render.activeChange.type(false, fabricMachine)"
                    :width="render.activeChange.width"
                    align="center"
                    text-size="mini"
                    @click="toggleStatus(fabricMachine)"
                />

                <!-- attract: Описание СМ -->
                <AppLabel
                    :text="render.description.data(fabricMachine)"
                    :title="render.description.title"
                    :type="render.description.type()"
                    :width="render.description.width"
                    align="left"
                    text-size="mini"
                />

            </div>

        </div>

    </div>

    <!-- attract: Модальное окно для подтверждений -->
    <AppModalAsyncMultiLine
        ref="appModalAsync"
        :text="modalText"
        :type="modalType"
        mode="confirm"
    />

    <!-- attract: Callout -->
    <AppCallout
        :show="calloutShow"
        :text="calloutText"
        :type="calloutType"
    />

</template>

<script setup>

import {reactive, ref} from 'vue'

import {useFabricsStore} from '/resources/js/src/stores/FabricsStore.js'

import AppCallout from '/resources/js/src/components/ui/callouts/AppCallout.vue'
import AppModalAsyncMultiLine from '/resources/js/src/components/ui/modals/AppModalAsyncMultiline.vue'
import AppLabelMultiLine from '/resources/js/src/components/ui/labels/AppLabelMultiLine.vue'
import AppLabel from '/resources/js/src/components/ui/labels/AppLabel.vue'
// import {FABRIC_ROLL_STATUS} from "@/src/app/constants/fabrics.js";


const fabricsStore = useFabricsStore()

// attract: Получаем список всех стегальных машин
const getFabricsMachines = async () => {
    const machines = await fabricsStore.getFabricsMachines()
    return machines.filter(machine => machine.id !== 0).sort((a, b) => a.id - b.id) // сортируем по id, без id == 0
}
const fabricsMachines = ref(await getFabricsMachines())

console.log(fabricsMachines.value)

// attract: Получаем ссылку на модальное для подтверждений окно с асинхронной функцией
const appModalAsync = ref(null)
const modalText = ref([])
const modalType = ref('danger')


// attract: Callout для вывода ошибок и предупреждений
const calloutType = ref('danger')
const calloutText = ref('')
const calloutShow = ref(false)
const calloutClose = (delay = 5000) => setTimeout(() => calloutShow.value = false, delay) // закрываем callout


const getType = (isHeader) => isHeader ? 'primary' : 'dark'


const render = reactive({
    id: {
        header: ['Код', 'CM'],
        width: 'w-[60px]',
        show: true,
        title: 'ID стегальной машины',
        type: (isHeader = false) => getType(isHeader),
        data: (fabricMachine) => fabricMachine.id.toString(),
    },
    name: {
        header: ['Название', 'CM'],
        width: 'w-[120px]',
        show: true,
        title: 'Название стегальной машины',
        type: (isHeader = false) => getType(isHeader),
        data: (fabricMachine) => fabricMachine.name,
    },
    shortName: {
        header: ['Сокращенное', 'название CM'],
        width: 'w-[120px]',
        show: true,
        title: 'Сокращенное название стегальной машины',
        type: (isHeader = false) => getType(isHeader),
        data: (fabricMachine) => fabricMachine.short_name,
    },
    active: {
        header: ['Статус', 'CM'],
        width: 'w-[130px]',
        show: true,
        title: 'Статус стегальной машины',
        type: (isHeader = false, fabricMachine = null) => isHeader ? getType(isHeader) : fabricMachine?.active ? 'success' : 'danger',
        data: (fabricMachine) => fabricMachine.active ? 'Активна' : 'Не активна',
    },
    activeChange: {
        header: ['Изменить', 'статус СМ'],
        width: 'w-[130px]',
        show: true,
        title: 'Изменить статус стегальной машины',
        type: (isHeader = false, fabricMachine = null) => isHeader ? getType(isHeader) : fabricMachine?.active ? 'danger' : 'success',
        data: (fabricMachine) => fabricMachine.active ? 'Сделать неактивной' : 'Сделать активной',
    },
    description: {
        header: ['Описание', 'CM'],
        width: 'w-[300px]',
        show: true,
        title: 'Описание стегальной машины',
        type: (isHeader = false) => getType(isHeader),
        data: (fabricMachine) => fabricMachine.description ? fabricMachine.description : '...',
    },
})


// attract: Изменение статуса стегальной машины
const toggleStatus = async (fabricMachine) => {

    if (fabricMachine.active) {

        modalText.value = [
            'Предупреждение!',
            'Все записи в незавершенных сменных заданиях',
            'связанные с этой стегальной машиной будут удалены!',
            'Сделать статус стегальной машины неактивным?'
        ]
        modalType.value = 'danger'

    } else {

        modalText.value = [
            'Сделать машину активной?'
        ]
        modalType.value = 'success'
    }

    const answer = await appModalAsync.value.show() // показываем модалку и ждем ответ
    if (answer) {
        fabricMachine.active = !fabricMachine.active
        const res = await fabricsStore.setFabricsMachineStatusById(fabricMachine.id, fabricMachine.active)
        console.log(res)
    }




}


</script>

<style scoped>

</style>
