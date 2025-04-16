<template>

    <div class="m-2 p-2 w-max-fit">

        <form @submit.prevent="formSubmit">

            <div class="bg-slate-200 border-2 rounded-lg border-slate-400 p-2 size-fit">

                <div class="flex">

                    <!-- attract: Код по 1С -->
                    <AppInputText
                        id="code_1C"
                        v-model.trim="fabric.code_1C"
                        :value="fabric.code_1C"
                        label="Код по 1С"
                        placeholder="Введите код по 1С"
                        width="w-[100px]"

                    />

                    <!-- attract: Название ПС -->
                    <AppInputText
                        id="name"
                        v-model.trim="fabric.name"
                        :value="fabric.name"
                        label="Название ПС"
                        placeholder="Введите название ПС"
                        width="w-[350px]"
                    />
                </div>

                <div class="flex">
                    <!-- attract: Средняя длина рулона ткани, м.п. -->
                    <AppInputNumberSimple
                        id="average_textile_length"
                        v-model="fabric.buffer.average_length"
                        :value="fabric.buffer.average_length"
                        label="Средняя длина рулона ткани, м.п."
                        placeholder="Введите ср. длину рул. ткани"
                        step="0.1"
                        width="w-[230px]"
                    />


                    <!-- attract: Буфер, м.п. -->
                    <AppInputNumberSimple
                        id="buffer_meters"
                        v-model="fabric.buffer.amount"
                        :value="fabric.buffer.amount"
                        label="Буфер, м.п."
                        placeholder="Введите остаток ПС (буфер)"
                        step="0.1"
                        width="w-[230px]"
                    />

                    <!-- attract: Буфер, рул. -->
                    <AppInputNumberSimple
                        id="buffer_rolls"
                        v-model="fabric.buffer.amount"
                        :disabled="true"
                        :value="fabric.buffer.amount"
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
                        v-model="fabric.buffer.min_rolls"
                        :value="fabric.buffer.min_rolls"
                        label="Минимальный запас ПС, рул."
                        placeholder="Введите мин. запас"
                        step="1"
                        width="w-[230px]"
                    />

                    <!-- attract: Макс. запас ПС, рул. -->
                    <AppInputNumberSimple
                        id="max_buffer_rolls"
                        v-model="fabric.buffer.max_rolls"
                        :value="fabric.buffer.max_rolls"
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
                        v-model="fabric.buffer.optimal_party"
                        :value="fabric.buffer.optimal_party"
                        label="Оптим. партия для запуска, м.п."
                        placeholder="Введите ОПЗ"
                        step="1"
                        width="w-[230px]"
                    />

                    <!-- attract: Коэффициент перевода ткани в ПС -->
                    <AppInputNumberSimple
                        id="rate"
                        v-model="fabric.buffer.rate"
                        :value="fabric.buffer.rate"
                        label="Коэфф. перевода ткани в ПС"
                        placeholder="Введите коэфф. перевода"
                        step="0.001"
                        width="w-[230px]"
                    />

                    <!-- attract: Производительность, м.п./ч. -->
                    <AppInputNumberSimple
                        id="productivity"
                        v-model="fabric.buffer.productivity"
                        :value="fabric.buffer.productivity"
                        label="Производительность, м.п./ч."
                        placeholder="Введите производительность"
                        step="0.001"
                        width="w-[230px]"
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
                                width="w-[355px]"

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
                                width="w-[355px]"
                                @checked="checkedHandlerRarity"
                            />
                        </div>
                    </div>

                </div>

                <!-- attract: Описание ПС -->
                <AppInputTextAreaSimple
                    id="descr"
                    v-model.trim="fabric.text.description"
                    :rows=2
                    :value="fabric.text.description"
                    class="cursor-pointer"
                    height="min-h-[60px]"
                    label="Описание полотна стеганного"
                    placeholder="Заполните описание ПС"
                    width="w-[705px]"
                />

<!--                <div class="mt-8">-->
<!--                    <AppSelect-->
<!--                        id="machine"-->
<!--                        :multiple="false"-->
<!--                        :selectData="selectData"-->
<!--                        label="Выберите стегальную машину"-->
<!--                        @change="changeHandler"-->
<!--                    />-->
<!--                </div>-->


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
// import {reactive} from 'vue'

import {useRoute, useRouter} from 'vue-router'

import {useFabricsStore} from '/resources/js/src/stores/FabricsStore.js'

import {NEW_FABRIC} from '/resources/js/src/app/constants/fabrics.js'

import AppInputText from '/resources/js/src/components/ui/inputs/AppInputText.vue'
import AppInputNumberSimple from '/resources/js/src/components/ui/inputs/AppInputNumberSimple.vue'
import AppInputButton from '/resources/js/src/components/ui/inputs/AppInputButton.vue'
import AppCheckbox from '/resources/js/src/components/ui/checkboxes/AppCheckbox.vue'
import AppInputTextAreaSimple from '/resources/js/src/components/ui/inputs/AppInputTextAreaSimple.vue'
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
let fabric = NEW_FABRIC

// attract: Получаем ПС
if (editMode) {
    fabric = await fabricStore.getFabricById(route.params.id)
}

// attract: Получаем список Стегальных машин
const machinesList = await fabricStore.getFabricsMachines()

console.log('fabric: ', fabric)
console.log('machines: ', machinesList)

// todo Формируем список для отображения Стегальных машин
const selectData = {
    name: 'fabric_machines',
    data: []
}

selectData.data = machinesList.map(machine => {
    return {
        id: machine.id,
        name: machine.short_name,
        // selected: machine.id === fabric.fabric_machine.id
        selected: false
    }
})

// Меняем Стегальную машину
// const changeHandler = (obj) => {
//     // console.log(obj)
//     fabric.fabric_machine.id = obj.id
//     fabric.fabric_machine.short_name = obj.name
//
//     console.log(fabric)
// }

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
    // todo Сделать валидацию формы обновления рабочего
    console.log(fabric)

    let res
    if (editMode) {
        res = await fabricStore.updateFabric(fabric)
    } else {
        console.log('create')

        res = await fabricStore.createFabric(fabric)

    }

    console.log('res', res)

    await router.push({name: 'manufacture.cell.fabrics.show'})      // переходим к списку сотрудников
}


</script>

<style scoped>

</style>
