<template>
    <form @submit.prevent="formSubmit">
        <div class="mt-2 ml-2">
            <AppInputText
                id="name"
                v-model.trim="fabric.name"
                :value="fabric.name"
                label="Название ПС"
                placeholder="Введите название ПС"
            />

            <AppInputText
                id="code_1C"
                v-model.trim="fabric.code_1C"
                :value="fabric.code_1C"
                label="Код по 1С"
                placeholder="Введите код по 1С"
            />

            <AppInputText
                id="description"
                v-model.trim="fabric.description"
                :value="fabric.description"
                label="Описание ПС"
                placeholder="Введите описание ПС"
            />

            <AppInputNumber
                id="buffer"
                v-model="fabric.buffer"
                :value="fabric.buffer"
                label="Буфер, м.п."
                placeholder="Введите остаток ПС (буфер)"
                step="0.1"
            />

            <AppInputNumber
                id="optimal_party"
                v-model="fabric.optimal_party"
                :value="fabric.optimal_party"
                label="Оптимальная партия для запуска, м.п. (ОПЗ)"
                placeholder="Введите оптимальную партию для запуска (ОПЗ)"
                step="0.1"
            />


            <div class="mt-8">
                <AppSelect
                    id="machine"
                    :multiple="false"
                    :selectData="selectData"
                    label="Выберите стегальную машину"
                    @change="changeHandler"
                />
            </div>

            <div class="mt-8">
                <AppCheckbox
                    id="active"
                    :checkboxData="checkboxData"
                    dir="horizontal"
                    inputType="radio"
                    legend="Статус"
                    type="secondary"
                    width="w-[600px]"
                    @checked="checkedHandler"
                />
            </div>

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
</template>

<script setup>
import {reactive} from 'vue'

import {useFabricsStore} from '/resources/js/src/stores/FabricsStore.js'

import {useRoute, useRouter} from 'vue-router'

import AppInputText from '/resources/js/src/components/ui/inputs/AppInputText.vue'
import AppInputNumber from '/resources/js/src/components/ui/inputs/AppInputNumber.vue'
import AppInputButton from '/resources/js/src/components/ui/inputs/AppInputButton.vue'
import AppSelect from '/resources/js/src/components/ui/selects/AppSelect.vue'
import AppCheckbox from '/resources/js/src/components/ui/checkboxes/AppCheckbox.vue'

const fabricStore = useFabricsStore()

const route = useRoute()
const router = useRouter()

// console.log('meta', route.meta.mode)
const editMode = route.meta.mode === 'edit'

console.log('route: ', route)

// Задаем пустое ПС для добавления
let fabric = {
    id: 0,
    active: true,
    name: '',
    buffer: 0,
    code_1C: '',
    fabric_machine: {
        id: 1,
        short_name: 'Нет данных'
    },
    optimal_party: 0,
    description: '',
}

// attract: Получаем ПС
if (editMode) {
    fabric = await fabricStore.getFabricById(route.params.id)
}

const machinesList = await fabricStore.getFabricsMachines()

console.log('fabric: ', fabric)
console.log('machines: ', machinesList)


// Формируем список для отображения Стегальных машин
const selectData = {
    name: 'fabric_machines',
    data: []
}

selectData.data = machinesList.map(machine => {
    return {
        id: machine.id,
        name: machine.short_name,
        selected: machine.id === fabric.fabric_machine.id
    }
})

// Меняем Стегальную машину
const changeHandler = (obj) => {
    // console.log(obj)
    fabric.fabric_machine.id = obj.id
    fabric.fabric_machine.short_name = obj.name

    console.log(fabric)
}

// Формируем данные для отображения стуса
const checkboxData = {
    name: 'status',
    data: [
        {id: 1, name: 'Активный', checked: fabric.active},
        {id: 2, name: 'Архив', checked: !fabric.active},
    ]
}


// Меняем статус
const checkedHandler = (obj) => {
    console.log(obj)
    fabric.active = obj.id === 1
}


// Отправляем форму на сервер
const formSubmit = async () => {
    // todo Сделать валидацию формы обновления рабочего
    console.log(fabric)

    let res = ''
    if (editMode) {
        res = await fabricStore.updateFabric(fabric)
    } else {
        console.log('create')

        res = await fabricStore.createFabric(fabric)

    }

    console.log('res', res)

    router.push({name: 'manufacture.cell.fabrics.show'})      // переходим к списку сотрудников
}


</script>

<style scoped>

</style>
