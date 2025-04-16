<template>
    <form @submit.prevent="formSubmit">
        <div class="mt-2 ml-2">
            <AppInputText
                id="surname"
                v-model.trim="worker.surname"
                :value="worker.surname"
                label="Фамилия"
                placeholder="Введите фамилию"
            />

            <AppInputText
                id="name"
                v-model.trim="worker.name"
                :value="worker.name"
                label="Имя"
                placeholder="Введите имя"
            />

            <AppInputText
                id="patronymic"
                v-model.trim="worker.patronymic"
                :value="worker.patronymic"
                label="Отчество"
                placeholder="Введите отчество"
            />

            <div class="mt-8">
                <AppSelect
                    id="cell_item"
                    :multiple="false"
                    :selectData="selectData"
                    label="Выберите ПЯ"
                    @change="changeHandler"
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

                <router-link :to="{name: 'workers'}">
                    <AppInputButton
                        id="submitButton"
                        func="button"
                        title="К списку сотрудников"
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

import {useWorkersStore} from '/resources/js/src/stores/WorkersStore.js'
import {useCellsGroupsStore} from '/resources/js/src/stores/CellsGroupsStore.js'


import {useRoute} from 'vue-router'
import {useRouter} from 'vue-router'

import AppInputText from '/resources/js/src/components/ui/inputs/AppInputText_.vue'
import AppInputButton from '/resources/js/src/components/ui/inputs/AppInputButton.vue'
import AppSelect from '/resources/js/src/components/ui/selects/AppSelect.vue'


const workersStore = useWorkersStore()
const cellsGroupsStore = useCellsGroupsStore()

const route = useRoute()
const router = useRouter()

console.log('meta', route.meta.mode)
const editMode = route.meta.mode === 'edit'

// Получаем данные по id рабочего, если редактирование или формируем новый объект
let worker = reactive({
    id: 0,
    surname: '',
    name: '',
    patronymic: '',
    cell_item_id: 1,
    cell_item_name: ''
})

if (editMode) {
    worker = await workersStore.getWorkerById(route.params.id)
}

// Получаем список ПЯ
const cellsList = await cellsGroupsStore.getCells()

// console.log(worker)
// console.log(cellsList)


// Формируем список для отображения ПЯ
const selectData = {
    name: 'cells_items',
    data: []
}

selectData.data = cellsList.map(cell => {
    return {
        id: cell.id,
        name: cell.name,
        selected: cell.id === worker.cell_item_id
    }
})


// Меняем ПЯ
const changeHandler = (obj) => {
    // console.log(obj)
    worker.cell_item_id = obj.id
    worker.cell_item_name = obj.name
}

const formSubmit = async () => {
    // todo Сделать валидацию формы обновления рабочего
    worker.surname = surname.value
    console.log(worker)

    let res =''
    if (editMode) {
        res = await workersStore.updateWorker(worker)
    } else {
        console.log('create')

        res = await workersStore.createWorker(worker)

    }

    console.log('res', res)

    router.push({name: 'workers'})      // переходим к списку сотрудников
}

</script>


<style scoped>

</style>
